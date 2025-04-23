<?php

namespace App\Exception;

class InvalidCoffeeOrderException extends \Exception
{
    private $errors;

    public function __constructor(array $errors)
    {
        parent::__construct('Commande invalide');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}