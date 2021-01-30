<?php

namespace App\Http\Controller;

use App\Domain\Premium\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    /**
     * @Route("/premium", name="premium")
     */
    public function premium(PlanRepository $planRepository, EntityManagerInterface $em): Response
    {
        $plans = $planRepository->findall();

        return $this->render('pages/premium.html.twig', [
            'plans' => $plans,
            'menu' => 'premium',
        ]);
    }
}
