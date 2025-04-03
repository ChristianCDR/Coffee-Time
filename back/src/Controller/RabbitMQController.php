<?php

namespace App\Controller;

use App\Service\RabbitMQService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RabbitMQController extends AbstractController
{
    private $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
    }

    #[Route('/api/queues', name: 'app_rabbit_queues', methods: ['GET'])]
    public function getQueues(): JsonResponse
    {
        $queues = $this->rabbitMQService->getQueues();
        return new JsonResponse($queues);
    }
}
