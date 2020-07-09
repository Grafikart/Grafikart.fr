<?php

namespace App\Http\Admin\Controller;

use App\Domain\Premium\Repository\PlanRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    /**
     * @Route("/premium", name="premium_index")
     */
    public function index(PlanRepository $planRepository): Response
    {
        $plans = $planRepository->findAll();

        return $this->render('admin/premium/index.html.twig', [
            'menu' => 'premium',
            'plans' => $plans,
        ]);
    }
}
