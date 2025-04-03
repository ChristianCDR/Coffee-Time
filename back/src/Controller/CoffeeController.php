<?php

namespace App\Controller;

use App\Entity\CoffeeOrder;
use App\Repository\CoffeeOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Message\CoffeeMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/api')]
final class CoffeeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, CoffeeOrderRepository $coffeeOrderRepository)
    {
        $this->entityManager = $entityManager;
        $this->coffeeOrderRepository = $coffeeOrderRepository;
    }

    #[Route('/order/history', name: 'app_order_history', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $orders = $this->coffeeOrderRepository->findAll();
        $data = [];

        if (!$orders) {
            return new JsonResponse ([
                'error' => 'Historique vide !'
            ], JsonResponse:: HTTP_NOT_FOUND);
        }

        foreach($orders as $order) {
            $data[] = [
                'orderID' => $order->getOrderID(),
                'name' => $order->getName(), 
                'intensity' => $order->getIntensity(), 
                'size' => $order->getSize(),
                'createdAt' => $order->getCreatedAt(),
                'executedAt' => $order->getExecutedAt()
            ];
        }

        return new JsonResponse ($data, JsonResponse::HTTP_OK);
    }

    #[Route('/order', name: 'app_order', methods: ['POST'])]
    public function prepareCoffee(MessageBusInterface $messageBus, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $orderId = uniqid();
        $message = new CoffeeMessage($orderId);
        $messageBus->dispatch($message);

        $order = new CoffeeOrder();

        $order
            ->setOrderID($orderId)
            ->setName($data['name'])
            ->setIntensity($data['intensity'])
            ->setSize($data['size'])
            ->setCreatedAt(new \DateTime())
        ;

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'Commande reçue !',
            'orderId' => 'Votre numéro de commande est '.$orderId
        ]);
    }

    #[Route('/order/edit', name: 'app_order_edit', methods: ['PUT'])]
    public function edit(Request $request): JsonResponse
    {
        $data= json_decode($request->getContent(), true);

        $order = $this->coffeeOrderRepository->findOneBy(['orderID' => $data['orderId']]);

        if (!$order) {
            return new JsonResponse([
                'error' => 'Cette commande n\'existe pas.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $order->setExecutedAt(new \DateTime());

        $this->entityManager->flush();

        return new JsonResponse ($data, JsonResponse::HTTP_OK);
    }

    #[Route('/order/delete', name: 'app_order_delete', methods: ['DELETE'])]
    public function delete(Request $request): JsonResponse
    {
        $data= json_decode($request->getContent(), true);

        $order = $this->coffeeOrderRepository->findOneBy(['orderID' => $data['orderId']]);

        if (!$order) {
            return new JsonResponse([
                'error' => 'Cette commande n\'existe pas.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($order);

        $this->entityManager->flush();

        return new JsonResponse ([
            'orderId' => $data['orderId']
        ], JsonResponse::HTTP_OK);
    }
}
