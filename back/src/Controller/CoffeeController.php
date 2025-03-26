<?php

namespace App\Controller;

use App\Message\CoffeeMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;

final class CoffeeController extends AbstractController
{
    #[Route('/coffee', name: 'app_coffee')]
    public function prepareCoffee(MessageBusInterface $messageBus): JsonResponse
    {
        $orderId = uniqid();
        $message = new CoffeeMessage($orderId);
        $messageBus->dispatch($message);

        return new JsonResponse([
            'status' => 'Commande envoyée!',
            'orderId' => $orderId
        ]);
    }
}
