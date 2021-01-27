<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\GroupMember;
use App\Form\GroupType;
use App\Repository\GroupMemberRepository;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group")
 */
class GroupController extends AbstractController
{
    /**
     * @Route("/", name="group_index", methods={"GET"})
     */
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="group_new", methods={"GET","POST"})
     */
    public function new(Request $request, GroupRepository $groupRepository, UserRepository $userRepository): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if (!$groupRepository->findOneBy(["name" => $group->getName()])) {

                $form_data = $request->request->get('group');


                // dd();
                $group->setCreatedAt(new DateTime('now'));
                $entityManager->persist($group);

                foreach ($form_data['member'] as $g) {
                    $user = $userRepository->find($g);
                    if ($user) {
                        $groupMember = new GroupMember();
                        $groupMember->setBelongsTo($group);
                        $groupMember->setUser($user);
                        $groupMember->setCreatedAt(new DateTime('now'));
                        $entityManager->persist($groupMember);
                    }
                }

                $entityManager->flush();
                $this->addFlash("success", "Group registered successfully!!");

                return $this->redirectToRoute('group_index');
            } else {
                $this->addFlash("danger", "Group Name already exists");
            }
        }

        return $this->render('group/new.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_show", methods={"GET","POST"})
     */
    public function show(Group $group, Request $request, UserRepository $userRepository, GroupMemberRepository $groupMemberRepository): Response
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

                return $this->redirectToRoute('group_show', ["id" => $group->getId()]);
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

            return $this->redirectToRoute('group_show', ["id" => $group->getId()]);
        }


        return $this->render('group/show.html.twig', [
            'group' => $group,
            'students' => $userRepository->findBy(["userType" => 1]),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('group_index');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Group $group): Response
    {
        if ($this->isCsrfTokenValid('delete' . $group->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('group_index');
    }
}
