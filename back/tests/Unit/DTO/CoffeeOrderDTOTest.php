<?php

namespace App\Tests\DTO;

use App\DTO\CoffeeOrderDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CoffeeOrderDTOTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidOrderDTO(): void
    {
        $dto = new CoffeeOrderDTO();

        $dto->name = "espresso";
        $dto->intensity = "light";
        $dto->size = "medium";

        $errors = $this->validator->validate($dto);
        $this->assertCount(0, $errors);
    }

    public function testInvalidOrderDTO(): void
    {
        $dto = new CoffeeOrderDTO();

        $dto->name = "Latte";
        $dto->intensity = "light";
        $dto->size = "extra-large";

        $errors = $this->validator->validate($dto);
        $this->assertGreaterThan(0, count($errors));
    }
}