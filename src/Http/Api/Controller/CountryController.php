<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CountryController extends AbstractController
{
    #[Route(path: '/country', name: 'country')]
    #[IsGranted('ROLE_USER')]
    public function index(): JsonResponse
    {
        $user = $this->getUserOrThrow();
        $country = $user->getCountry();
        $countries = Countries::getNames();
        if ($country) {
            $countries = array_merge([$country => $countries[$country]], $countries);
        }

        return $this->json($countries);
    }
}
