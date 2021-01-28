<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectFileDetail;
use App\Entity\SimilarityHistory;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\SimilarityHistoryRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            $uploadedFile = $form['document']->getData();
            if ($uploadedFile->getClientOriginalExtension() == "docx") {
                $destination = $this->getParameter('kernel.project_dir') . '/public/documents';
                $newFilename = $this->getUser()->getId() . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
                $uploadedFile->move($destination, $newFilename);




                $project->setDocLocation($newFilename);
                $project->setUploadedAt(new DateTime('now'));
                $project->setUploadedBy($this->getUser());
                $project->setStatus(0);


                $entityManager->persist($project);



                $cmd = "python3 " . $this->getParameter('kernel.project_dir') . "/public/load.py " . $destination . "/" . $newFilename . " 2>&1";

                $res = shell_exec($cmd);
                $res = explode("\n", $res)[0];
                $res = explode(",", $res);
                // dd($res);

                $projecfileDetail = new ProjectFileDetail();
                $projecfileDetail->setProject($project);
                $projecfileDetail->setNoOfParagraph(0);
                $projecfileDetail->setWordCount((int)$res[0]);
                $projecfileDetail->setSentenceCount((int)$res[1]);
                $projecfileDetail->setDistinctWordCount((int)$res[2]);
                $projecfileDetail->setCreatedAt(new DateTime('now'));

                $entityManager->persist($projecfileDetail);



                $entityManager->flush();

                $this->addFlash("success", "Project uploaded");
                return $this->redirectToRoute('project_index');
            } else {
                $this->addFlash("danger", "file type not accepted");
            }
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET","POST"})
     */
    public function show(Project $project, Request $request, SimilarityHistoryRepository $similarityHistoryRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($request->query->get('check')) {

            $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();

            $destination = $this->getParameter('kernel.project_dir') . '/public/documents/';
            $this_file = $destination . $project->getDocLocation();
            $project->setCheckedAt(new DateTime('now'));
            $project->setStatus(1);


            foreach ($projects as $pro) {



                if ($pro->getId() != $project->getId()) {

                    $cmd = "python3 " . $destination . "/checksim.py  " . $this_file . " " . $destination . "" . $pro->getDocLocation() . " 2>&1";
                    // $cmd = "python3 " . $this->getParameter('kernel.project_dir') . "/public/load.py " . $destination . "cov.docx " . $destination . "cov2.docx 2>&1";

                    $res = shell_exec($cmd);
                    //   dd($res);
                    $res = (float)$res * 100;

                    $projectHistory = $similarityHistoryRepository->findOneBy(["project" => $pro]);
                    if (!$projectHistory)
                        $projectHistory = new SimilarityHistory();
                    $projectHistory->setCheckedProject($project);
                    $projectHistory->setProject($pro);
                    $projectHistory->setSimilarity($res);
                    $projectHistory->setCheckedAt(new DateTime('now'));
                    $projectHistory->setCheckedBy($this->getUser());

                    // if(!$projectHistory)
                    $entityManager->persist($projectHistory);
                    $entityManager->flush();
                }
            }

            $this->addFlash("success", "checked successfully. view History below");
            return $this->redirectToRoute('project_show', ["id" => $project->getId()]);
        }


        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/view/similarity", name="view_similarity", methods={"GET","POST"})
     */
    public function viewSimilarity(Request $request, ProjectRepository $projectRepository)
    {
        if ($request->request->get('check')) {
            $f1 = $request->request->get('original');
            $f2 = $request->request->get('to_be_checked');

            $project1 = $projectRepository->find($f1);
            $project2 = $projectRepository->find($f2);
          $result=[];
            // $handle = fopen($this->getParameter('kernel.project_dir') . "/public/documents/" . "cov.docx.txt", "r");
            // if ($handle) {
            //     while (($line = fgets($handle)) !== false) {
            //         if(!empty($line)){
            //         foreach ($project2->getFileContents() as $fc) {
                     

            //             if(!empty($fc->getContent())){
                       
            //             $pos = strpos(trim(preg_replace('/\s\s+/', ' ', $line)."."), trim(preg_replace('/\s\s+/', ' ', $fc->getContent())));

            //             dump(trim(preg_replace('/\s\s+/', ' ', $line)));
            //             dump($pos);
            //             dd(trim(preg_replace('/\s\s+/', ' ', $fc->getContent())));
            //             // Note our use of ===.  Simply == would not work as expected
            //             // because the position of 'a' was the 0th (first) character.
            //             if ($pos) {
                        
            //                 $result[]=$line." at paragraph:-".$fc->getParagraph();
            //             }
            //         }
            //         }}
            //     }

            //     fclose($handle);
            // } else {
            //     // error opening the file.
            // }

            // dd($result);
        }
        return $this->render('project/show.sim.html.twig', [
            'project1' => $project1,
            'project2' => $project2,
       
        ]);
    }
    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
