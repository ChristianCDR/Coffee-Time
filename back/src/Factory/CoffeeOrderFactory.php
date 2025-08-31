<?php

namespace App\Factory;

use App\Entity\CoffeeOrder;
use App\DTO\CoffeeOrderDTO;
use Symfony\Component\Uid\Uuid;

class CoffeeOrderFactory
{
    public function createOrderFromDto(CoffeeOrderDTO $dto): CoffeeOrder
    {
        $order = new CoffeeOrder();

        $order
            ->setOrderID(Uuid::v4()->toRfc4122())
            ->setName($dto->name)
            ->setIntensity($dto->intensity)
            ->setSize($dto->size)
            ->setCreatedAt(new \Datetime())
        ;
        return $order;
    }
}