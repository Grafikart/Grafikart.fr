<?php

namespace App\Http\Controller\Template;

use App\Domain\Premium\Repository\SubscriptionRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PremiumStateController extends AbstractController
{
    public function state(SubscriptionRepository $repository): Response
    {
        $user = $this->getUserOrThrow();
        $subscription = $repository->findCurrentForUser($user);

        return $this->render('partials/user_premium.html.twig', [
            'subscription' => $subscription,
            'user' => $user,
        ]);
    }
}
