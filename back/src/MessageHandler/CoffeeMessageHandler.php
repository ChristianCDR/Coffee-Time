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
        foreach ([10, 30, 60, 100] as $progress) {
            sleep(2);
            
            // Mettre à jour la progression avec Mercure
            $update = new Update(
                "http://localhost/process/coffee", // Le topic
                json_encode(["status" => "running", "progress" => $progress]) // Les données
            );

            try {
                $this->hub->publish($update);
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de la publication Mercure : ' . $e->getMessage());
            }    
        }

    }
}