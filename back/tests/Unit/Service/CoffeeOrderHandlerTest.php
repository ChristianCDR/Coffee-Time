<?php

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\CoffeeOrder;
use App\DTO\CoffeeOrderDTO;
use Psr\Log\LoggerInterface;
use App\Message\CoffeeMessage;
use App\Service\CoffeeOrderHandler;
use App\Factory\CoffeeOrderFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use App\Exception\InvalidCoffeeOrderException;
use Doctrine\ORM\EntityManagerInterface;

class CoffeeOrderHandlerTest extends TestCase
{
    private $serializer;
    private $validator;
    private $entityManager;
    private $messageBus;
    private $factory;
    private $logger;
    private $violationList;
    
    
    public function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->factory = $this->createMock(CoffeeOrderFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->violationList = $this->createMock(ConstraintViolationListInterface::class);
    }

    private function getCoffeeOrderHandler(): CoffeeOrderHandler
    {
        return new CoffeeOrderHandler(
            $this->serializer,
            $this->validator,
            $this->entityManager,
            $this->messageBus,
            $this->factory,
            $this->logger
        );
    }

    public function testCoffeeOrderHandleSuccess(): void
    {
        $dto = new CoffeeOrderDTO();
        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'small';

        $order = new CoffeeOrder();
        $order
            ->setOrderID('U71c773e3-5206-49e6-8eda-fd29f0ebb79e')
            ->setName('espresso')
            ->setIntensity('strong')
            ->setSize('small')
            ->setCreatedAt(new \Datetime('2025-04-04 21:15:07'), new \DateTimeZone('UTC'))
        ;

        $message = new CoffeeMessage('test-order-id');

        $request = new Request(content: json_encode(
            [
                'name' => 'espresso',
                'intensity' => 'strong',
                'size' => 'small'
            ]
        ));

        $this->serializer->method('deserialize')->willReturn($dto);

        $this->violationList->method('count')->willReturn(0);

        $this->validator->method('validate')->willReturn($this->violationList);

        $this->factory->method('createOrderFromDto')->willReturn($order);

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function($message) {
                    return $message instanceof CoffeeMessage;
                })
            )
            ->willReturn(new Envelope($message))
        ;

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();

        $response = $coffeeOrderHandler->handle($request);

        $this->assertInstanceOf(CoffeeOrder::class, $response);
        $this->assertEquals('espresso', $response->getName());
    }

    public function testDeserializeRequestSuccess(): void
    {
        $dto = new CoffeeOrderDTO();

        $this->serializer->method('deserialize')->willReturn($dto);

        $request = new Request(content: json_encode(
            [
                'name' => 'espresso',
                'intensity' => 'strong',
                'size' => 'small'
            ]
        ));

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();

        $response = $coffeeOrderHandler->deserializeRequest($request);
        
        $this->assertInstanceOf(CoffeeOrderDTO::class, $response);
    }

    public function testDeserializeRequestThrowsException(): void
    { 
        $this->serializer->method('deserialize')->willThrowException(new InvalidCoffeeOrderException(['error' => 'Format JSON invalide.']));

        $request = new Request(content: 'invalid_json');

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();

        $this->expectException(InvalidCoffeeOrderException::class);
        $this->expectExceptionMessage('Commande invalide');

        $response = $coffeeOrderHandler->deserializeRequest($request);
        
        $this->assertArrayHasKey('error', $response);
    }

    public function testValidateDTOSuccess(): void
    {
        $dto = new CoffeeOrderDTO();
        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'small';

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();
        $coffeeOrderHandler->validateDTO($dto);

        $this->assertTrue(true);

    }

    public function testValidateDTOThrowsException(): void
    {
        $dto = new CoffeeOrderDTO();
        $dto->name = 'espreso';
        $dto->intensity = 'strong';
        $dto->size = 'small';

        $errors = [
            'property' => 'name',
            'message' =>  'Le nom doit être parmi les choix autorisés.'
        ];

        $this->validator->method('validate')->willThrowException(new InvalidCoffeeOrderException($errors));

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();

        $this->expectException(InvalidCoffeeOrderException::class);

        $coffeeOrderHandler->validateDTO($dto);
    }

    public function testCoffeeOrderHandleFailsOnDispatch(): void
    {
        $dto = new CoffeeOrderDTO();
        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'small';

        $order = new CoffeeOrder();
        $order
            ->setOrderID('U71c773e3-5206-49e6-8eda-fd29f0ebb79e')
            ->setName('espresso')
            ->setIntensity('strong')
            ->setSize('small')
            ->setCreatedAt(new \Datetime('2025-04-04 21:15:07'), new \DateTimeZone('UTC'))
        ;

        $this->serializer->method('deserialize')->willReturn($dto);

        $this->violationList->method('count')->willReturn(0);
        $this->validator->method('validate')->willReturn($this->violationList);

        $this->factory->method('createOrderFromDto')->willReturn($order);

        $this->entityManager->expects($this->once())->method('persist')->with($order);
        $this->entityManager->expects($this->once())->method('flush');

        $exception = new HandlerFailedException(new Envelope(new \stdClass()), [new \Exception('AMQP down')]);

        $this->messageBus->method('dispatch')->willThrowException($exception);

        $request = new Request(content: json_encode(
            [
                'name' => 'espresso',
                'intensity' => 'strong',
                'size' => 'large'
            ]
        ));

        $coffeeOrderHandler = $this->getCoffeeOrderHandler();

        $this->expectException(HandlerFailedException::class);

        $coffeeOrderHandler->handle($request);
    }
}