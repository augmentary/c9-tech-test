<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryController extends AbstractController
{
    #[Route('/country', name: 'app_country', methods: ['GET'])]
    public function index(
        CountryRepository $repository,
        NormalizerInterface $normalizer
    ): JsonResponse {
        $countries = $repository->findAll();

        // given more time I'd use something more selective here, so we're explicitly including the relevant fields
        // e.g. serializer groups or separate response DTOs - just doing this to get values to the frontend quickly
        return $this->json([
            'data' => $normalizer->normalize($countries)
        ]);
    }
}
