<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DockerService;

final class ProcessController extends AbstractController
{
    private $dockerService;

    public function __construct(DockerService $dockerService)
    {
        $this->dockerService = $dockerService;
    }

    #[Route('/stop-process', name: 'app_stop_process', methods: ["POST"])]
    public function stopContainer(): JsonResponse
    {
        $containerName = 'coffee-message-consumer';

        if ($this->dockerService->stopContainer($containerName)) {
            return new JsonResponse([
                'status' => 'Conteneur RabbitMQ arrêté avec succès.'
            ], JsonResponse::HTTP_OK);
        }
        
        return new JsonResponse([
            'status' => 'Erreur lors de l\'arrêt du conteneur.'
        ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
