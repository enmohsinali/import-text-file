<?php

namespace App\Controller;

use App\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class LogAnalyzerController extends AbstractController
{
    #[Route('/count', name: 'app_log_analyzer', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        // $doctrine = $this->getDoctrine()->getManager();
        $logs = $doctrine->getRepository(Log::class)->findAll();
        // $entityAsArray = $this->serializer->normalize($logs, null);
        var_dump($logs);
        return $this->json($logs);
    }
}
