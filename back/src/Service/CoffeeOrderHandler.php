<?php

namespace App\Service;

use App\Entity\CoffeeOrder;
use App\DTO\CoffeeOrderDTO;
use App\Message\CoffeeMessage;
use App\Factory\CoffeeOrderFactory;
use App\Repository\CoffeeOrderRepository;
use App\Exception\InvalidCoffeeOrderException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CoffeeOrderHandler
{
    private $serializer;
    private $validator;
    private $entityManager;
    private $messageBus;
    private $factory;
    private $logger;
    private $coffeeOrderRepository;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        CoffeeOrderFactory $factory,
        LoggerInterface $logger,
        CoffeeOrderRepository $coffeeOrderRepository
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->factory = $factory;
        $this->logger = $logger;
        $this->coffeeOrderRepository = $coffeeOrderRepository;
    }

    public function handleCreateOrder(Request $request): CoffeeOrder
    {
        $dto = $this->deserializeRequest($request);

        $this->validateDTO($dto);

        $order = $this->factory->createOrderFromDto($dto);

        $message = new CoffeeMessage($order->getOrderID());

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        try {
            $this->messageBus->dispatch($message);
        }
        catch (HandlerFailedException $e) {
            $this->logger->error("Erreur lors de l'envoi AMQP : ". $e->getMessage());
            throw $e;
        }

        return $order;
    }

    public function deserializeRequest(Request $request): CoffeeOrderDTO
    {
        $data = $request->getContent();

        try {
            $dto = $this->serializer->deserialize($data, CoffeeOrderDTO::class, 'json');
        }
        catch (NotEncodableValueException $e) {
            $this->logger->warning('Erreur de désérialisation : '. $e->getMessage());
            throw new InvalidCoffeeOrderException(['error' => 'Format JSON invalide.']);
        }

        return $dto;
    }

    public function validateDTO(CoffeeOrderDTO $dto): void
    {
        $violations = $this->validator->validate($dto);

        if(count($violations) > 0) {
            $errors =[];

            foreach($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ];
            }
            $this->logger->info('Violations sur CoffeeOrderDTO', ['errors' => $errors]);
            throw new InvalidCoffeeOrderException($errors);
        }
    }

    public function handleAction(Request $request, string $action): string
    {
        $data= json_decode($request->getContent(), true);

        if(!isset($data['orderId']) || !is_string($data['orderId'])) {
            throw new InvalidCoffeeOrderException(['error' => 'orderId est manquant ou invalide.']);
        }

        $order = $this->coffeeOrderRepository->findOneBy(['orderID' => $data['orderId']]);

        if (!$order) {
            throw new InvalidCoffeeOrderException(['error' => 'Cette commande n\'existe pas.']);
        }

        switch ($action) {
            case 'edit':
                $order->setExecutedAt(new \DateTime());
                break;
        
            case 'delete':
                $this->entityManager->remove($order);
                break;
        
            default:
                throw new InvalidCoffeeOrderException(['error' => "Action '$action' non supportée."]);
        }

        $this->entityManager->flush();

        return $order->getOrderID();
    }
}