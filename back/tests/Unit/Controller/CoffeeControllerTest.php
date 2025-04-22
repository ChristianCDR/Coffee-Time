<?php

namespace App\Tests\Unit\Controller;

use App\Controller\CoffeeController;
use App\Message\CoffeeMessage;
use App\Repository\CoffeeOrderRepository;
use App\DTO\CoffeeOrderDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Messenger\Envelope;

class CoffeeControllerTest extends TestCase
{
    private $serializer;
    private $validator;
    private $messageBus;
    private $logger;
    private $entityManager;
    private $violationList;
    private $coffeeOrderRepository;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->violationList = $this->createMock(ConstraintViolationListInterface::class);
        $this->coffeeOrderRepository = $this->createMock(CoffeeOrderRepository::class);
    }

    public function getController(): CoffeeController
    {
        return new CoffeeController(
            $this->entityManager,
            $this->coffeeOrderRepository,
            $this->serializer,
            $this->validator,            
            $this->logger
        );
    }

    public function testPrepareCoffeeSuccess(): void
    {
        $dto = new CoffeeOrderDTO();

        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'large';

        $this->serializer->method('deserialize')->willReturn($dto);

        $this->violationList->method('count')->willReturn(0);
        $this->validator->method('validate')->willReturn($this->violationList);

        $message = new CoffeeMessage(uniqid());
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($message) {
                return $message instanceof CoffeeMessage;
            }))
            ->willReturn(new Envelope($message));
        ;

        $request = new Request(content: json_encode(
                [
                    'name' => 'espresso',
                    'intensity' => 'strong',
                    'size' => 'large'
                ]
            ))
        ;

        $controller = $this->getController();

        $response = $controller->prepareCoffee($this->messageBus, $request);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('orderId', $content);
    }

    public function testPrepareCoffeeInvalidJson(): void
    {
        $controller = $this->getController();

        $this->serializer->method('deserialize')->willThrowException(new \Exception('Invalid JSON'));

        $request = new Request(content: 'Invalid Json');
        
        $response = $controller->prepareCoffee($this->messageBus, $request);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPrepareCoffeeValidationErrors(): void
    {
        $controller = $this->getController();

        $dto = new CoffeeOrderDTO();
        $this->serializer->method('deserialize')->willReturn($dto);

        $this->violationList->method('count')->willreturn(2);

        $this->validator->method('validate')->willReturn($this->violationList);

        $request = new Request(content: json_encode(
            [
                'name' => 'Latte',
                'intensity' => 'strong',
                'size' => 'extra-large'
            ]
        ));

        $response = $controller->prepareCoffee($this->messageBus, $request);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPrepareCoffeeDispatchFails(): void
    {
        $controller = $this->getController();

        $dto = new CoffeeOrderDTO();

        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'large';

        $this->serializer->method('deserialize')->willReturn($dto);

        $this->violationList->method('count')->willReturn(0);
        $this->validator->method('validate')->willReturn($this->violationList);

        $this->messageBus->method('dispatch')->willThrowException(new \Exception('AMQP down'));

        $request = new Request(content: json_encode(
                [
                    'name' => 'espresso',
                    'intensity' => 'strong',
                    'size' => 'large'
                ]
            ))
        ;

        $response = $controller->prepareCoffee($this->messageBus, $request);
        $this->assertEquals(JsonResponse::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
    }
}

