<?php

namespace App\Service;

use Firebase\JWT\JWT;

class JwtService 
{
    private $secret;

    public function __construct () 
    {
        $this->secret = $_ENV['MERCURE_JWT_SECRET'];
    }

    public function generateJwt(array $payload): string
    {
        if (!$this->secret) {
            throw new \Exception('Secret not found');
        }

        $jwt = JWT::encode($payload, $this->secret, 'HS256');

        return $jwt;
    }

}