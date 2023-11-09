<?php

namespace App\Tests\Api;

use App\Entity\Country;
use App\Entity\ShippingMethod;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeliveryDateControllerTest extends WebTestCase
{
    public function testGetDeliveryDate(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();

        $country = new Country();
        $country->setName('GB');
        $country->setIsoCode('GB');
        $country->setIsEurope(true);
        $em->persist($country);

        $method = new ShippingMethod();
        $method->setName('Test shipping method');
        $method->setShipTimeUk(8);
        $em->persist($method);

        $em->flush();


        $params = [
            'country' => $country->getIsoCode(),
            'shippingMethod' => $method->getId()->toBase58(),
            'orderDate' => '2023-11-08T12:00:00Z',
        ];
        $client->request('GET', '/api/v1/delivery-date?'.http_build_query($params));
        self::assertResponseIsSuccessful();

        $response = $client->getResponse();
        $json = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertEquals(['data' => ['expected_delivery_date' => '2023-11-20']], $json);
    }
}
