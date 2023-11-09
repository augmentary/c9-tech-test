<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\ShippingMethod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['App'];
    }

    public function load(ObjectManager $manager): void
    {
        $countries = [
            'GB' => ['United Kingdom of Great Britain and Northern Ireland', true],
            'FR' => ['France', true],
            'US' => ['United States of America', false],
        ];

        foreach($countries as $code => [$name, $isEurope]) {
            $country = new Country();
            $country->setIsoCode($code);
            $country->setName($name);
            $country->setIsEurope($isEurope);

            $manager->persist($country);
        }


        $shippingMethods = [
            'Royal Mail' => [1, 3, 8],
        ];

        foreach($shippingMethods as $name => [$uk, $eur, $row]) {
            $method = new ShippingMethod();
            $method->setName($name);
            $method->setShipTimeUk($uk);
            $method->setShipTimeEurope($eur);
            $method->setShipTimeRestOfWorld($row);
            $manager->persist($method);
        }

        $manager->flush();
    }
}
