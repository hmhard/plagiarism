<?php

namespace App\Controller;

use App\Entity\Project;
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
            $destination = $this->getParameter('kernel.project_dir') . '/public/documents';
            $newFilename = $this->getUser()->getId().uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->move($destination, $newFilename);
            
            $project->setDocLocation($newFilename);
            $project->setUploadedAt(new DateTime('now'));
            $project->setUploadedBy($this->getUser());
            $project->setStatus(0);
           
            
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET","POST"})
     */
    public function show(Project $project, Request $request,SimilarityHistoryRepository $similarityHistoryRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if($request->query->get('check')){

            $projects=$this->getDoctrine()->getRepository(Project::class)->findAll();

            $destination = $this->getParameter('kernel.project_dir') . '/public/documents/';
            $this_file=$destination.$project->getDocLocation();
            $project->setCheckedAt(new DateTime('now'));
            $project->setStatus(1);


            foreach ($projects as $pro) {
                
                
                
                if($pro->getId()!=$project->getId()){
                    
                    $cmd="python3 ".$destination."/checksim.py  ".$this_file ." ".$destination."".$pro->getDocLocation()." 2>&1";
                    
                    $res=shell_exec($cmd);
                    $res=(float)$res*100;
                    // dd($res);

                    $projectHistory= $similarityHistoryRepository->findOneBy(["project"=>$pro]);
                    if(!$projectHistory)
                    $projectHistory= new SimilarityHistory();
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

            $this->addFlash("success","checked successfully. view History below");
            return $this->redirectToRoute('project_show',["id"=>$project->getId()]);
        }

        
        return $this->render('project/show.html.twig', [
            'project' => $project,
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
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
