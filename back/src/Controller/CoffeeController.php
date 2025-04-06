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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

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
    #[OA\Get(
        summary: 'Get orders history',
        tags: ['Orders'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get all orders history',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'orderID', type: 'string', example: '67f04bdb53bbe'),
                            new OA\Property(property: 'name', type: 'string', example: 'espresso'),
                            new OA\Property(property: 'intensity', type: 'string', example: 'strong'),
                            new OA\Property(property: 'size', type: 'string', example: 'small'),
                            new OA\Property(
                                property: 'createdAt',
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'date', type: 'string', example: '2025-04-04 21:15:07.000000'),
                                    new OA\Property(property: 'timezone_type', type: 'integer', example: 3),
                                    new OA\Property(property: 'timezone', type: 'string', example: 'UTC')
                                ]
                            ),
                            new OA\Property(
                                property: 'executedAt',
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'date', type: 'string', example: '2025-04-04 21:15:07.000000'),
                                    new OA\Property(property: 'timezone_type', type: 'integer', example: 3),
                                    new OA\Property(property: 'timezone', type: 'string', example: 'UTC')
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'History empty',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'History empty')
                    ]
                )
            )
        ]
    )]
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
    #[OA\Post(
        summary: 'Create a new order',
        tags: ['Orders'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['name', 'intensity', 'size'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'espresso'),
                    new OA\Property(property: 'intensity', type: 'string', example: 'strong'),
                    new OA\Property(property: 'size', type: 'string', example: 'small'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: "Commande reçue !"),
                        new OA\Property(property: 'orderId', type: 'string', example: "Votre numéro de commande est 67f2e84ab634e")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request body',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Invalid request body')
                    ]
                )
            )
        ]
    )]
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
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/order/edit', name: 'app_order_edit', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Set order as executed',
        tags: ['Orders'],
        requestBody: new OA\RequestBody(
            description: 'ID of the order to be set as executed',
            content: new OA\JsonContent(
                type: 'object',
                required: ['orderId'],
                properties: [
                    new OA\Property(property: 'orderId', type: 'string', example: '67f04bdb53bbe')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'OrderId', type: 'string', example: '67f04bdb53bbe')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Order not found')
                    ]
                )
            )
        ]
    )]
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

        return new JsonResponse ([
            'orderId' => $data['orderId']
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/order/delete', name: 'app_order_delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete an order',
        tags: ['Orders'],
        requestBody: new OA\RequestBody(
            description: 'ID of the order to delete',
            content: new OA\JsonContent(
                type: 'object',
                required: ['orderId'],
                properties: [
                    new OA\Property(property: 'orderId', type: 'string', example: '67f1bab751880')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'OrderId', type: 'string', example: '67f1bab751880')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Order not found')
                    ]
                )
            )
        ]
    )]
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
