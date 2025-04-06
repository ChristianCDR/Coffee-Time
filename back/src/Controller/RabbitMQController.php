<?php

namespace App\Controller;

use App\Service\RabbitMQService;
use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RabbitMQController extends AbstractController
{
    private $rabbitMQService;
    private $jwtService;

    public function __construct(RabbitMQService $rabbitMQService, JwtService $jwtService)
    {
        $this->rabbitMQService = $rabbitMQService;
        $this->jwtService = $jwtService;
    }

    #[Route('/api/queues', name: 'app_rabbit_queues', methods: ['GET'])]
    public function getQueues(): JsonResponse
    {
        $queues = $this->rabbitMQService->getQueues();

        if (empty($queues)) {
            return new JsonResponse(['error' => 'No queues found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = [
            'mercure' => [
                'subscribe' => ['http://localhost/queues']
            ]
        ];

        $jwt = $this->jwtService->generateJwt($payload);

        if (!$jwt) {
            return new JsonResponse(['error' => 'Failed to generate suscribe JWT'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'queues' => $queues,
            'jwt' => $jwt
        ], JsonResponse::HTTP_OK);
    }
}
