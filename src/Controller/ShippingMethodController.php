<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ShippingMethodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingMethodController extends AbstractController
{
    #[Route('/shipping-method', name: 'app_shipping_method', methods: ['GET'])]
    public function index(
        ShippingMethodRepository $repository,
        NormalizerInterface $normalizer,
    ): JsonResponse
    {
        $methods = $repository->findAll();

        // given more time I'd use something more formalized here, so we're explicitly including the relevant fields
        // e.g. serializer groups or separate response DTOs - just doing this to get values to the frontend quickly
        return $this->json([
            'data' => $normalizer->normalize($methods),
        ]);
    }
}
