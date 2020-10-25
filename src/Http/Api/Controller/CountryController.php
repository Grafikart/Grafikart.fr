<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    /**
     * @Route("/country", name="country")
     * @IsGranted("ROLE_USER")
     */
    public function index(): JsonResponse
    {
        return $this->json(Countries::getNames());
    }
}
