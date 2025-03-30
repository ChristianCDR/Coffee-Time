<?php

namespace App\MessageHandler;

use App\Message\CoffeeMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class CoffeeMessageHandler
{
    private $hub;
    private $logger;

    public function __construct(HubInterface $hub, LoggerInterface $logger)
    {
        $this->hub = $hub;
        $this->logger = $logger;
    }

    public function __invoke(CoffeeMessage $message)
    {
        
        // Simuler un travail long
        for ($i = 0; $i <= 5; $i++) {
            sleep(1);
            
            // Mettre à jour la progression
            $update = new Update(
                'coffee/progress', // Le topic
                json_encode(['progress' => $i]),
                true
            );

            try {
                $this->hub->publish($update);
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de la publication Mercure : ' . $e->getMessage());
            }    
        }

    }
}