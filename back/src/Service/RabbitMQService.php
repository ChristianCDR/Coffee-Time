<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQService
{
    private $connection;
    private $hub;
    private $username;
    private $password;

    public function __construct(HubInterface $hub)
    {
        $this->username = $_ENV['RABBITMQ_USER'];
        $this->password = $_ENV['RABBITMQ_PASSWORD'];

        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, $this->username, $this->password, '/');
        $this->hub = $hub;
    }

    public function getQueues()
    {
        $channel = $this->connection->channel();
        // Récupération des informations des queues
        $apiUrl = 'rabbitmq:15672/api/queues';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");

        $response = curl_exec($ch);

        // Vérification d'erreur
        if ($response === false) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Erreur cURL : $errorMessage");
        }

        curl_close($ch);

        $queues = json_decode($response, true);

        if (!$queues) {
            throw new \Exception('Erreur lors du décodage de la réponse de RabbitMQ.');
        }

        // Envoi de l'update en temps réel avec Mercure
        $update = new Update(
            '/queues',  // Le topic Mercure
            json_encode($queues)
        );
        $this->hub->publish($update);

        return $queues;
    }
}
