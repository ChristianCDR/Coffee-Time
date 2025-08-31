<?php

namespace App\Tests\Unit\Factory;

use App\Entity\CoffeeOrder;
use App\DTO\CoffeeOrderDTO;
use App\Factory\CoffeeOrderFactory;
use PHPUnit\Framework\TestCase;

class CoffeeOrderFactoryTest extends TestCase
{
    public function testCoffeeOrderFactorySuccess(): void
    {
        $dto = new CoffeeOrderDTO();
        $dto->name = 'espresso';
        $dto->intensity = 'strong';
        $dto->size = 'small';

        $coffeeOrderFactory = new CoffeeOrderFactory();
        $response = $coffeeOrderFactory->createOrderFromDto($dto);

        $this->assertInstanceOf(CoffeeOrder::class, $response);
        $this->assertEquals('espresso', $response->getName());
    }
}