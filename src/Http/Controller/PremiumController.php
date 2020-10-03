<?php

namespace App\Http\Controller;

use App\Domain\Premium\PremiumService;
use App\Domain\Premium\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    /**
     * @Route("/premium", name="premium")
     */
    public function premium(PlanRepository $planRepository, PremiumService $premiumService, EntityManagerInterface $em): Response
    {
        /**
        $this->getUser()->setPremiumEnd(new \DateTime());
        $em->flush();
        $payment = new Payment();
        $payment->planId = 2;
        $payment->id= '1AZEAZE';
        $payment->amount = 12;
        $premiumService->recordPayment($payment);
         * **/
        $plans = $planRepository->findall();

        return $this->render('pages/premium.html.twig', [
            'plans' => $plans,
        ]);
    }
}
