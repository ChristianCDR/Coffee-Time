<?php

namespace App\MessageHandler;

use App\Message\CoffeeMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CoffeeMessageHandler
{
    public function __invoke(CoffeeMessage $message)
    {
        echo "Préparation de la commande " . $message->getOrderId() ." en cours...". "\n";
        sleep(15); // Simulation du temps de préparation
        echo "La commande " . $message->getOrderId() ." est prête!". "\n";
    }
}