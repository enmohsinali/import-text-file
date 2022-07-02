<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\LogParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class LogAnalyzerController extends AbstractController
{
    #[Route('/count', name: 'app_log_analyzer', methods: ['GET'])]
    public function index(Request $request,ManagerRegistry $doctrine): JsonResponse
    {
        $serviceNames = $request->query->get('serviceNames');
        
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $statusCode = $request->query->get('statusCode');

        $logsCount = $doctrine->getRepository(Log::class)->queryCount($serviceNames,$startDate,$endDate,$statusCode);
        
        return $this->json(["counter" => $logsCount]);
    }
}
