<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ConsumerService;
use Psr\Log\LoggerInterface;

#[Route('/api/admin')]
final class ProcessController extends AbstractController
{

    public function __construct(
        private ConsumerService $consumerService,
        private LoggerInterface $logger
    ) {}

    #[Route('/stop-process', name: 'app_stop_process', methods: ["POST"])]
    public function stopConsumer(): JsonResponse
    {
        $this->consumerService->requestStop('async');

        return new JsonResponse([
            'status' => 'La requête d\'arrêt a été prise en compte.'
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/restart-process', name: 'app_restart_process', methods: ["POST"])]
    public function restartConsumer(): JsonResponse
    {
        $this->consumerService->clearStopFlag('async');
        
        return new JsonResponse([
            'status' => 'La requête de redémarrage a été prise en compte.'
        ], JsonResponse::HTTP_OK);
    }
}
