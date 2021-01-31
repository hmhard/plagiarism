<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\GroupMember;
use App\Entity\Project;
use App\Entity\ProjectFileDetail;
use App\Entity\User;
use App\Form\GroupType;
use App\Form\GroupType2;
use App\Form\ProjectType;
use App\Repository\GroupMemberRepository;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectSessionRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student")
     */
    public function index(GroupRepository $groupRepository): Response
    {
        $groups=$groupRepository->getMyGroups(["user"=>$this->getUser()]);

        return $this->render('student/index.html.twig', [


            'groups' => $groups,
        ]);

    
    }

     /**
     * @Route("/student/project/upload", name="student_project_upload", methods={"GET","POST"})
     */
    public function newProject(Request $request,ProjectSessionRepository $projectSessionRepository, ProjectRepository $projectRepository): Response
    {
        if(!$projectSessionRepository->getActiveProject()){
            $this->addFlash("danger", "No Active Project Session");
            return $this->redirectToRoute('student');
        }
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            if (!$projectRepository->findOneBy(["projectSession" => $project->getProjectSession(),"ownerGroup"=>$project->getOwnerGroup()])) {

           
            if (!$projectRepository->findOneBy(["title" => $project->getTitle()])) {
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
                return $this->redirectToRoute('student');
            
            }
             else {
                $this->addFlash("danger", "file type not accepted");
            }
        }

             else {
                $this->addFlash("danger", "Similar Project Title");
            }
        }
        else {
            $this->addFlash("danger",  sprintf("This Group already uploaded ".$project->getProjectSession()->getName()));
        }
    }

        return $this->render('student/project.new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }


          /**
     * @Route("/new", name="student_group_new", methods={"GET","POST"})
     */
    public function new(Request $request, GroupRepository $groupRepository, UserRepository $userRepository): Response
    {

        $group = new Group();
        $form = $this->createForm(GroupType2::class, $group)
        ->add('member'
          
        , EntityType::class,
         [
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) {

                $res = $er->createQueryBuilder('u')
             
                    ->where('u.owngroup is NULL')
                    ->andWhere('u.userType=1')
                    ->orderBy('u.id', 'ASC');
                ;
             
                return $res;
            },
           
            'placeholder' => 'Add Member',
             "mapped"=>false,
             "multiple"=>true,

        ]
        )
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if (!$groupRepository->findOneBy(["name" => $group->getName()])) {

                $form_data = $request->request->get('group');


                // dd();
                $group->setCreatedAt(new \DateTime('now'));
                $entityManager->persist($group);

                foreach ($form_data['member'] as $g) {
                    $user = $userRepository->find($g);
                    if ($user) {
                        $groupMember = new GroupMember();
                        $groupMember->setBelongsTo($group);
                        $groupMember->setUser($user);
                        $groupMember->setCreatedAt(new \DateTime('now'));
                        $entityManager->persist($groupMember);
                    }
                }
                $groupMember = new GroupMember();
                $groupMember->setBelongsTo($group);
                $groupMember->setUser($this->getUser());
                $groupMember->setCreatedAt(new \DateTime('now'));
                $entityManager->persist($groupMember);

                $entityManager->flush();
                $this->addFlash("success", "Group registered successfully!!");

                return $this->redirectToRoute('student');
            } else {
                $this->addFlash("danger", "Group Name already exists");
            }
        }

        return $this->render('student/group.new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
       /**
     * @Route("/{id}/group/edit", name="student_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group): Response
    {

        $users=[];
    
    foreach($group->getGroupMembers() as $user){
        $users[]=$user->getUser();
    }
        $form = $this->createForm(GroupType2::class, $group)
        ->add('member'
          
        , EntityType::class,
         [
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) {

                $res = $er->createQueryBuilder('u')
             
                    ->where('u.owngroup is NULL')
                    ->andWhere('u.userType=1')
                    ->orderBy('u.id', 'ASC');
                ;
             
                return $res;
            },
            'choices' => $users,
            'placeholder' => 'Add Member',
             "mapped"=>false,
             "multiple"=>true,

        ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student');
        }

        return $this->render('student/group.edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
       /**
     * @Route("/{id}/project/edit", name="student_project_edit", methods={"GET","POST"})
     */
    public function projectEdit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        $entityManager=$this->getDoctrine()->getManager();
            
        if ($form->isSubmitted() && $form->isValid()) {
            
          

            if (!$projectRepository->findOneBy(["title" => $project->getTitle()])) {
                $uploadedFile = $form['document']->getData();
                if ($uploadedFile->getClientOriginalExtension() == "docx") {
                    $destination = $this->getParameter('kernel.project_dir') . '/public/documents';
                    $newFilename = $this->getUser()->getId() . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
                    $uploadedFile->move($destination, $newFilename);
    
    
    
    
                    $project->setDocLocation($newFilename);
                    $project->setUploadedAt(new \DateTime('now'));
                    $project->setUploadedBy($this->getUser());
                   
    
    
                    $cmd = "python3 " . $this->getParameter('kernel.project_dir') . "/public/load.py " . $destination . "/" . $newFilename . " 2>&1";
    
                    $res = shell_exec($cmd);
                    $res = explode("\n", $res)[0];
                    $res = explode(",", $res);
                    // dd($res);

                    $projecfileDetail=$this->getDoctrine()->getRepository(ProjectFileDetail::class)->findOneBy(["project"=>$project]);
    
                    if(!$projecfileDetail)
                    $projecfileDetail = new ProjectFileDetail();
                    $projecfileDetail->setProject($project);
                    $projecfileDetail->setNoOfParagraph(0);
                    $projecfileDetail->setWordCount((int)$res[0]);
                    $projecfileDetail->setSentenceCount((int)$res[1]);
                    $projecfileDetail->setDistinctWordCount((int)$res[2]);
                    $projecfileDetail->setCreatedAt(new \DateTime('now'));
    
                    $entityManager->persist($projecfileDetail);
    
    
    
                    $entityManager->flush();
    
                    $this->addFlash("success", "Project Edited");
                    return $this->redirectToRoute('student');
                
                }
                 else {
                    $this->addFlash("danger", "file type not accepted");
                }
            }
    
                 else {
                    $this->addFlash("danger", "Similar Project Title");
                }



            return $this->redirectToRoute('student');
        }

        return $this->render('student/project.edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/{id}/project", name="student_project", methods={"GET","POST"})
     */
    public function myProjects(Group $group, Request $request): Response
    {


        return $this->render('student/project.show.html.twig', [
            'group' => $group,
          
        ]);
    }
      /**
     * @Route("/{id}/group", name="student_group_show", methods={"GET","POST"})
     */
    public function showGroup(Group $group, Request $request, UserRepository $userRepository, GroupMemberRepository $groupMemberRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


        if ($request->request->get('remove_member')) {
            $member_id = $request->request->get('member_id');
            $user = $userRepository->find($member_id);
            $groupMember = $groupMemberRepository->findOneBy(["user" => $user]);

            if ($groupMember) {

                $entityManager->remove($groupMember);
                $entityManager->flush();
                $this->addFlash("success", "successfully done!!");

                return $this->redirectToRoute('student_group_show', ["id" => $group->getId()]);
            }
        }
        if ($request->request->get('add_member')) {

            foreach ($request->request->get('member') as $m) {

                if (!$groupMemberRepository->findOneBy(["user" => $m, "belongsTo" => $group])) {

                    $groupMember = new GroupMember();
                    $groupMember->setUser($userRepository->find($m));
                    $groupMember->setBelongsTo($group);
                    $groupMember->setCreatedAt(new \DateTime('now'));
                    $entityManager->persist($groupMember);
                }
            }
            $entityManager->flush();
            $this->addFlash("success", "successfully Added!!");

            return $this->redirectToRoute('student', ["id" => $group->getId()]);
        }


        return $this->render('student/group.show.html.twig', [
            'group' => $group,
            'students' => $userRepository->findBy(["userType" => 1]),
        ]);
    }


}
