<?php

namespace App\Tests\Functional\Controller;

use App\Entity\CoffeeOrder;
use App\Repository\CoffeeOrderRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CoffeeControllerTest extends WebTestCase
{
    private $client;
    private $mockRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->mockRepository = $this->createMock(CoffeeOrderRepository::class);
        static::getContainer()->set(CoffeeOrderRepository::class, $this->mockRepository);
    }

    public function testIndexReturnsOrdersSuccessfully(): void
    {
        $order = new CoffeeOrder();

        $order->setOrderID(1)
            ->setName('Test Order')
            ->setIntensity('Medium')
            ->setSize('Large')
            ->setCreatedAt(new \DateTime())
            ->setExecutedAt(new \DateTime())
        ;

        $this->mockRepository->method('findAll')->willReturn([$order]);

        $this->client->request('GET', '/api/order/history');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $response);
        $this->assertEquals('Test Order', $response[0]['name']);
    }

    public function testIndexReturnsNoOrders(): void
    {
        $this->client->request('GET', '/api/order/history');

        $this->assertResponseStatusCodeSame(404);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Historique vide !', $response['error']);
    }

    public function testSuccessfulOrderCreation(): void
    {
        $this->client->request(
            'POST',
            '/api/order',
            server: ['Content-Type' => 'application/json'],
            content: json_encode(
                [
                    'name' => 'espresso',
                    'intensity' => 'strong',
                    'size' => 'large'
                ]
            )
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('orderId', $response);
    }

    public function testUnsuccessfulOrderCreation(): void
    {
        
    }
}
