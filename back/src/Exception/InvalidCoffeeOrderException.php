<?php

namespace App\Exception;

class InvalidCoffeeOrderException extends \Exception
{

    public function __construct(private array $errors)
    {
        parent::__construct('Commande invalide');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}