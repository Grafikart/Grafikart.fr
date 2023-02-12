<?php

declare(strict_types=1);

namespace App\Http\Controller\Account;

use App\Domain\Auth\User;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Payment\Stripe\StripeApi;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @method User getUser()
 */
class SubscriptionController extends AbstractController
{
    public function __construct(private readonly StripeApi $api)
    {
    }

    #[Route(path: '/profil/subscription', name: 'user_subscription', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function manage(): Response
    {
        $user = $this->getUser();
        $redirectUrl = $this->generateUrl('user_invoices', [], UrlGeneratorInterface::ABSOLUTE_URL);
        if (null === $user->getStripeId()) {
            $this->addFlash('error', "Vous n'avez pas d'abonnement actif");

            return $this->redirect($redirectUrl);
        }

        return $this->redirect($this->api->getBillingUrl($user, $redirectUrl));
    }
}
