<?php

namespace App\Tests\Functional\Controller;

use App\Entity\CoffeeOrder;
use App\Service\CoffeeOrderHandler;
use App\DataFixtures\CoffeeOrderFixtures;
use App\Exception\InvalidCoffeeOrderException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

final class CoffeeControllerTest extends WebTestCase
{
    private $client;
    private $mockCoffeeOrderHandler;
    protected $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->mockCoffeeOrderHandler = $this->createMock(CoffeeOrderHandler::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            CoffeeOrderFixtures::class
        ]);
        
    }

    public function testIndexReturnsOrdersSuccessfully(): void
    {
        $this->client->request('GET', '/api/order/history');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $response);
        $this->assertEquals('espresso', $response[0]['name']);
    }

    public function testIndexReturnsNoOrders(): void
    {
        $this->databaseTool->loadFixtures([]);
        
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
            server: ['CONTENT_TYPE' => 'application/json'],
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

    public function testPrepareCoffeeInvalidJson(): void
    {
        $this->client->request(
            'POST',
            '/api/order',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: 'invalid json'
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($this->client->getResponse()->getContent());

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $response);
    }

    public function testPrepareCoffeeReturnsServiceUnavailable(): void
    {
        $this->mockCoffeeOrderHandler->method('handleCreateOrder')->willThrowException(new \Exception('AMQP down'));

        static::getContainer()->set(CoffeeOrderHandler::class, $this->mockCoffeeOrderHandler);
        
        $this->client->request(
            'POST',
            '/api/order',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(
                [
                    'name' => 'espresso',
                    'intensity' => 'strong',
                    'size' => 'large'
                ]
            )
        );

        $this->assertResponseStatusCodeSame(503);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $response);
    }

    public function testEditCoffeeOrderSuccess(): void
    {
        $this->client->request(
            'PUT',
            '/api/order/edit',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['orderId' => 'U71c773e3-5206-49e6-8eda-fd29f0ebb79e'])
        );

        $this->assertResponseStatusCodeSame(200);
 
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('orderId', $response);
    }

    public function testEditCoffeeOrderThrowsException(): void
    {
        $this->mockCoffeeOrderHandler->method('handleAction')->willThrowException(new InvalidCoffeeOrderException(['error' => 'Commande introuvable.']));

        static::getContainer()->set(CoffeeOrderHandler::class, $this->mockCoffeeOrderHandler);

        $this->client->request(
            'PUT',
            '/api/order/edit',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['orderId' => '999'])
        );

        $this->assertResponseStatusCodeSame(400);
 
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $response);
    }

    public function testDeleteCoffeeOrderSuccess(): void
    {
        $this->client->request(
            'DELETE',
            '/api/order/delete',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['orderId' => 'U71c773e3-5206-49e6-8eda-fd29f0ebb79e'])
        );

        $this->assertResponseStatusCodeSame(200);
 
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('orderId', $response);
    }

    public function testDeleteCoffeeOrderThrowsException(): void
    {
        $this->mockCoffeeOrderHandler->method('handleAction')->willThrowException(new InvalidCoffeeOrderException(['error' => 'Commande introuvable.']));

        static::getContainer()->set(CoffeeOrderHandler::class, $this->mockCoffeeOrderHandler);

        $this->client->request(
            'DELETE',
            '/api/order/delete',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['orderId' => '999'])
        );

        $this->assertResponseStatusCodeSame(400);
 
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $response);
    }

}
