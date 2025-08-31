<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProcessControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testStopConsumerSuccess(): void
    {
        $this->client->request('POST', '/api/admin/stop-process');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('La requête d\'arrêt a été prise en compte.', $response['status']);
    }

    public function testRestartConsumerSuccess(): void
    {
        $this->client->request('POST', '/api/admin/restart-process');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('La requête de redémarrage a été prise en compte.', $response['status']);
    }
}