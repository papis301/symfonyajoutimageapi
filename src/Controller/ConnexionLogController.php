<?php

namespace App\Controller;

use App\Entity\ConnexionLog;
use App\Form\ConnexionLogType;
use App\Repository\ConnexionLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/connexion/log')]
final class ConnexionLogController extends AbstractController
{
    #[Route(name: 'app_connexion_log_index', methods: ['GET'])]
    public function index(ConnexionLogRepository $connexionLogRepository): Response
    {
        return $this->render('connexion_log/index.html.twig', [
            'connexion_logs' => $connexionLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_connexion_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $connexionLog = new ConnexionLog();
        $form = $this->createForm(ConnexionLogType::class, $connexionLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($connexionLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_connexion_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('connexion_log/new.html.twig', [
            'connexion_log' => $connexionLog,
            'form' => $form,
        ]);
    }

    #[Route('/ajout', name: 'ajout', methods: ['GET'])]
    public function ajout(ConnexionLog $connexionLog): Response
    {
        $log = new ConnexionLog();
            $log->setLoggedAt(new \DateTime());
            $log->setUserIdentifier($user->getUserIdentifier()); // ou autre identifiant

            $entityManager->persist($log);
            $entityManager->flush();
    }

    #[Route('/{id}', name: 'app_connexion_log_show', methods: ['GET'])]
    public function show(ConnexionLog $connexionLog): Response
    {
        return $this->render('connexion_log/show.html.twig', [
            'connexion_log' => $connexionLog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_connexion_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConnexionLog $connexionLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConnexionLogType::class, $connexionLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_connexion_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('connexion_log/edit.html.twig', [
            'connexion_log' => $connexionLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_connexion_log_delete', methods: ['POST'])]
    public function delete(Request $request, ConnexionLog $connexionLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$connexionLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($connexionLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_connexion_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
