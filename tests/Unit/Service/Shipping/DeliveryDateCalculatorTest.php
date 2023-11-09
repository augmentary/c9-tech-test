<?php

namespace App\Tests\Unit\Service\Shipping;

use App\Entity\Country;
use App\Entity\ShippingMethod;
use App\Service\Shipping\DeliveryDateCalculator;
use PHPUnit\Framework\TestCase;

class DeliveryDateCalculatorTest extends TestCase
{
    public function testGetDeliveryDateBefore4pm(): void
    {
        $method = new ShippingMethod();
        $method->setShipTimeUk(1);

        $country = new Country();
        $country->setIsoCode('GB');

        //a thursday before 4pm - friday delivery
        $orderDate = new \DateTimeImmutable('2023-11-09T12:00:00');

        $calc = new DeliveryDateCalculator(16, new \DateTimeZone('Europe/London'));
        $result = $calc->getDeliveryDate($method, $country, $orderDate);
        $this->assertEquals(new \DateTimeImmutable('2023-11-10T00:00:00'), $result);
        $this->assertEquals('Friday', $result->format('l'));
    }

    public function testGetDeliveryDateOverWeekend(): void
    {
        $method = new ShippingMethod();
        $method->setShipTimeUk(1);

        $country = new Country();
        $country->setIsoCode('GB');

        //a friday - monday delivery
        $orderDate = new \DateTimeImmutable('2023-11-17T12:00:00');

        $calc = new DeliveryDateCalculator(16, new \DateTimeZone('Europe/London'));
        $result = $calc->getDeliveryDate($method, $country, $orderDate);
        $this->assertEquals(new \DateTimeImmutable('2023-11-20T00:00:00'), $result);
        $this->assertEquals('Monday', $result->format('l'));
    }

    public function testGetDeliveryDateAfter4pm(): void
    {
        $method = new ShippingMethod();
        $method->setShipTimeUk(1);

        $country = new Country();
        $country->setIsoCode('GB');

        //a friday after 4pm - tuesday delivery
        $orderDate = new \DateTimeImmutable('2023-11-17T16:00:00');

        $calc = new DeliveryDateCalculator(16, new \DateTimeZone('Europe/London'));
        $result = $calc->getDeliveryDate($method, $country, $orderDate);
        $this->assertEquals(new \DateTimeImmutable('2023-11-21T00:00:00'), $result);
        $this->assertEquals('Tuesday', $result->format('l'));
    }

    public function testGetDeliveryDateMultipleWeeks(): void
    {
        $method = new ShippingMethod();
        $method->setShipTimeUk(15);

        $country = new Country();
        $country->setIsoCode('GB');

        //a monday before 4pm - monday delivery
        $orderDate = new \DateTimeImmutable('2023-11-13T15:59:00');

        $calc = new DeliveryDateCalculator(16, new \DateTimeZone('Europe/London'));
        $result = $calc->getDeliveryDate($method, $country, $orderDate);
        $this->assertEquals(new \DateTimeImmutable('2023-12-04T00:00:00'), $result);
        $this->assertEquals('Monday', $result->format('l'));
    }

    public function testDeliveryDuringBst(): void
    {
        $method = new ShippingMethod();
        $method->setShipTimeUk(1);

        $country = new Country();
        $country->setIsoCode('GB');

        $local = new \DateTimeZone('Europe/London');

        //a monday after 4pm localtime - wednesday delivery
        $orderDate = new \DateTimeImmutable('2023-06-12T15:59:00', new \DateTimeZone('UTC'));
        $this->assertEquals(
            '2023-06-12T16:59:00+01:00',
            $orderDate->setTimezone($local)->format(\DateTimeInterface::ATOM)
        );

        $calc = new DeliveryDateCalculator(16, $local);
        $result = $calc->getDeliveryDate($method, $country, $orderDate);
        $this->assertEquals(new \DateTimeImmutable('2023-06-14T00:00:00'), $result);
        $this->assertEquals('Wednesday', $result->format('l'));
    }
}
