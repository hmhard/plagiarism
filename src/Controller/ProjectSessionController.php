<?php

namespace App\Controller;

use App\Entity\ProjectSession;
use App\Form\ProjectSessionType;
use App\Repository\ProjectSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project-session")
 */
class ProjectSessionController extends AbstractController
{
    /**
     * @Route("/", name="project_session_index", methods={"GET"})
     */
    public function index(ProjectSessionRepository $projectSessionRepository): Response
    {
        return $this->render('project_session/index.html.twig', [
            'project_sessions' => $projectSessionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="project_session_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $projectSession = new ProjectSession();
        $form = $this->createForm(ProjectSessionType::class, $projectSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $projectSession->setCreatedAt(new \DateTime('now'));
            $entityManager->persist($projectSession);
            $entityManager->flush();

            return $this->redirectToRoute('project_session_index');
        }

        return $this->render('project_session/new.html.twig', [
            'project_session' => $projectSession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_session_show", methods={"GET"})
     */
    public function show(ProjectSession $projectSession): Response
    {
        return $this->render('project_session/show.html.twig', [
            'project_session' => $projectSession,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProjectSession $projectSession): Response
    {
        $form = $this->createForm(ProjectSessionType::class, $projectSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_session_index');
        }

        return $this->render('project_session/edit.html.twig', [
            'project_session' => $projectSession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProjectSession $projectSession): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectSession->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projectSession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_session_index');
    }
}
