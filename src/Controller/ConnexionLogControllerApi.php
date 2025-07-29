<?php

namespace App\Controller;

use App\Entity\ConnexionLog;
use App\Repository\ConnexionLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ConnexionLogController extends AbstractController
{
    #[Route('/log-connexion', name: 'log_connexion', methods: ['POST'])]
    public function logConnexion(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user'])) {
            return $this->json(['error' => 'User identifier missing'], 400);
        }

        $log = new ConnexionLog();
        $log->setUserIdentifier($data['user']);
        $log->setLoggedAt(new \DateTime());

        $em->persist($log);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Connexion enregistrée.']);
    }

    #[Route('/stats/connexions', name: 'stats_connexions', methods: ['GET'])]
    public function getConnexionStats(ConnexionLogRepository $repo): JsonResponse
    {
        $conn = $repo->createQueryBuilder('c')
            ->select('DATE(c.loggedAt) as date, COUNT(c.id) as count')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->json($conn);
    }
}
