<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Plan;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Exception\PaymentFailedException;
use App\Infrastructure\Payment\Paypal\PaypalService;
use App\Infrastructure\Payment\Stripe\StripeApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class PremiumController extends AbstractController
{
    /**
     * @Route("/premium/paypal/{orderId}", name="premium_paypal")
     * @IsGranted("ROLE_USER")
     */
    public function paypal(string $orderId, PaypalService $paypal, EventDispatcherInterface $dispatcher): JsonResponse
    {
        try {
            $payment = $paypal->createPayment($orderId);
            $payment = $paypal->capture($payment);
            $dispatcher->dispatch(new PaymentEvent($payment, $this->getUser()));

            return $this->json([]);
        } catch (PaymentFailedException $e) {
            return $this->json(['title' => 'Erreur lors du paiement', 'detail' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/premium/{id}/stripe/checkout", name="premium_stripe_checkout", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function stripe(Plan $plan, StripeApi $api, EntityManagerInterface $em): JsonResponse
    {
        try {
            $api->createCustomer($this->getUser());
            $em->flush();

            return $this->json([
                'id' => $api->createPaymentSession($this->getUser(), $plan),
            ]);
        } catch (\Exception $e) {
            return $this->json(['title' => "Impossible de contacter l'API Stripe"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
