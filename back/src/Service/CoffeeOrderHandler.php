<?php

namespace App\Service;

use App\Entity\CoffeeOrder;
use App\DTO\CoffeeOrderDTO;
use App\Message\CoffeeMessage;
use App\Factory\CoffeeOrderFactory;
use App\Exception\InvalidCoffeeOrderException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;

class CoffeeOrderHandler
{
    private $serializer;
    private $validator;
    private $entityManager;
    private $messageBus;
    private $factory;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        CoffeeOrderFactory $factory
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->factory = $factory;
    }

    public function handle(Request $request): CoffeeOrder
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
            throw new InvalidCoffeeOrderException($errors);
        }
    }
}