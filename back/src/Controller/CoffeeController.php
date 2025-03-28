<?php

namespace App\Controller;

use App\Message\CoffeeMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/api')]
final class CoffeeController extends AbstractController
{
    #[Route('/order', name: 'app_order', methods: ['POST'])]
    public function prepareCoffee(MessageBusInterface $messageBus, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $orderId = uniqid();
        $message = new CoffeeMessage($orderId);
        $messageBus->dispatch($message);

        return new JsonResponse([
            'status' => 'Commande reçue !',
            'orderId' => 'Votre numéro de commande est '.$orderId
        ]);
    }
}
