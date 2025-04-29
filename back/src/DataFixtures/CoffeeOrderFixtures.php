<?php

namespace App\DataFixtures;

use App\Entity\CoffeeOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CoffeeOrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $order = new CoffeeOrder();
        $order
            ->setOrderID('U71c773e3-5206-49e6-8eda-fd29f0ebb79e')
            ->setName('espresso')
            ->setIntensity('strong')
            ->setSize('small')
            ->setCreatedAt(new \Datetime('2025-04-04 21:15:07'), new \DateTimeZone('UTC'))
        ;

        $manager->persist($order);

        $manager->flush();
    }
}
