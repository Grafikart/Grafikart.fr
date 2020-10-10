<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Auth\User;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Infrastructure\Payment\Stripe\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    private StripeApi $stripeApi;
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;

    public function __construct(StripeApi $stripeApi, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $this->stripeApi = $stripeApi;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * @Route("/stripe/webhook", name="stripe_webhook")
     */
    public function index(Event $event): JsonResponse
    {
        switch ($event->type) {
            /*
            case 'invoice.paid':
                return $this->invoicePaid($event->data->object);
            **/
            case 'charge.refunded':
                return $this->onRefund($event->data['object']);
            case 'checkout.session.completed':
                return $this->onCheckoutCompleted($event->data['object']);
            default:
                return $this->json([]);
        }
    }

    /**
     * Un paiement "one shot" a été terminé sur stripe.
     */
    public function onCheckoutCompleted(Session $session): JsonResponse
    {
        $payment = new StripePayment($this->stripeApi->getPaymentIntent((string) $session->payment_intent), $session);
        $user = $this->em->getRepository(User::class)->findOneBy(['stripeId' => $session->customer]);
        if (null === $user) {
            return $this->json(['title' => "Impossible de trouver l'utilisateur correspondant à la transaction"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->dispatcher->dispatch(new PaymentEvent($payment, $user));

        return $this->json([]);
    }

    public function onRefund(Charge $charge): JsonResponse
    {
        $payment = new Payment();
        $payment->id = (string) $charge->payment_intent;
        $this->dispatcher->dispatch(new PaymentRefundedEvent($payment));

        return $this->json([]);
    }

    /*
    public function chargeSucceeded (Charge $charge): JsonResponse
    {
        dump(
            $charge,
            $this->stripeApi->getPaymentIntent($charge->payment_intent),
            $this->stripeApi->getCustomer($charge->customer)
        );
        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function invoicePaid (Invoice $invoice): JsonResponse
    {
        dump($invoice, $this->stripeApi->getCustomer($invoice->customer), $this->stripeApi->getSubscription($invoice->subscription));
        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);

        $payment = $this->paymentFactory->createPayment($invoice);
        $this->premiumService->recordPayment($payment);
        return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        // $payment =
        // $payment = $paypal->capture($payment);
        // $premiumService->recordPayment($payment);
        // return $this->json([]);
        // dump($invoice->customer);
        // return $this->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    **/
}
