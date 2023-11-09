<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Country;
use App\Entity\ShippingMethod;
use App\Service\Shipping\DeliveryDateCalculator;
use App\ValueResolver\Attribute\QueryStringParamToDateTime;
use App\ValueResolver\Attribute\QueryStringParamToEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryDateController extends AbstractController
{
    #[Route('/delivery-date', name: 'app_delivery_date', methods: ['GET'])]
    public function index(
        #[QueryStringParamToDateTime]
        \DateTimeImmutable $orderDate,
        #[QueryStringParamToEntity]
        ShippingMethod $shippingMethod,
        #[QueryStringParamToEntity(field: 'isoCode')]
        Country $country,
        DeliveryDateCalculator $calculator
    ): JsonResponse {
        $eta = $calculator->getDeliveryDate($shippingMethod, $country, $orderDate);
        return $this->json([
            'data' => [
                'expected_delivery_date' => $eta->format('Y-m-d'),
            ]
        ]);
    }
}
