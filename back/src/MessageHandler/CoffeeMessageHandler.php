<?php

namespace App\MessageHandler;

use App\Message\CoffeeMessage;
use App\Service\RabbitMQService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class CoffeeMessageHandler
{
    private $hub;
    private $rabbitMQService;
    private $logger;

    public function __construct(HubInterface $hub, RabbitMQService $rabbitMQService, LoggerInterface $logger)
    {
        $this->hub = $hub;
        $this->rabbitMQService = $rabbitMQService;
        $this->logger = $logger;
    }

    public function __invoke(CoffeeMessage $message)
    {
        // Simuler un travail long
        foreach ([10, 30, 60, 100] as $progress) {
            
            $queues = $this->rabbitMQService->getQueues();

            $data = [
                'progress' => $progress,
                'orderId'  => $message->getOrderId(),
            ];

            if ($progress >= 60) {
                $data['message'] = $queues;
            }

            $update = new Update(
                'https://example.com/books/1',  // Le topic Mercure
                json_encode($data),
            );

            try {
                $this->hub->publish($update);
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de la publication Mercure : ' . $e->getMessage());
            }

            sleep(2);
        }
    }
}