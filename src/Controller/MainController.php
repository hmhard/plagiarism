<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
   
/**
     * @Route("/",name="home")
     */
    public function index(UserRepository $userRepository ,ProjectRepository $projectRepository ,GroupRepository $groupRepository,UserTypeRepository $userTypeRepository): Response
    {
        $new_projects=$projectRepository->getCount(["status"=>[0]]);
      
        $accepted_projects=$projectRepository->getCount(["status"=>[2]]);
        $rejected_projects=$projectRepository->getCount(["status"=>[3]]);
        $total_projects=$projectRepository->getCount([]);
        $inactive_users=$userRepository->getCount(["isActive"=>false]);
        $active_users=$userRepository->getCount(["isActive"=>true]);
        $total_users=$userRepository->getCount([]);
        return $this->render('main/index.html.twig', [
         "userTypes"=>$userTypeRepository->findAll(),
         "new_projects"=>$new_projects,
         "accepted_projects"=>$accepted_projects,
         "rejected_projects"=>$rejected_projects,
         "total_projects"=>$total_projects,
         "inactive_users"=>$inactive_users,
         "active_users"=>$active_users,
         "total_users"=>$total_users,
         
        ]);
    }
}
