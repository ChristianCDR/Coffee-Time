<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DockerService;

#[Route('/api')]
final class ProcessController extends AbstractController
{
    private $dockerService;

    public function __construct(DockerService $dockerService)
    {
        $this->dockerService = $dockerService;
    }

    #[Route('/start-process', name: 'app_start_process', methods: ["POST"])]
    public function startContainer(): JsonResponse
    {
        $containerName = 'coffee-message-consumer';

        if ($this->dockerService->startContainer($containerName)) {
            return new JsonResponse([
                'status' => 'Conteneur démarré avec succès.'
            ], JsonResponse::HTTP_OK);
        }
        
        return new JsonResponse([
            'status' => 'Erreur lors du démarrage du conteneur.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/stop-process', name: 'app_stop_process', methods: ["POST"])]
    public function stopContainer(): JsonResponse
    {
        $containerName = 'coffee-message-consumer';

        if ($this->dockerService->stopContainer($containerName)) {
            return new JsonResponse([
                'status' => 'Processus arrêté avec succès.'
            ], JsonResponse::HTTP_OK);
        }
        
        return new JsonResponse([
            'status' => 'Erreur lors de l\'arrêt du processus.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/restart-process', name: 'app_restart_process', methods: ["POST"])]
    public function restartContainer(): JsonResponse
    {
        $containerName = 'coffee-message-consumer';

        if ($this->dockerService->restartContainer($containerName)) {
            return new JsonResponse([
                'status' => 'Conteneur redémarré avec succès.'
            ], JsonResponse::HTTP_OK);
        }
        
        return new JsonResponse([
            'status' => 'Erreur lors du redémarrage du conteneur.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
