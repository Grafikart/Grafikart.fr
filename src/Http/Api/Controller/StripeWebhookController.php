<?php

declare(strict_types=1);

namespace App\Http\Api\Controller;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Plan;
use App\Domain\Premium\Entity\Subscription;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Infrastructure\Payment\Stripe\StripePaymentFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stripe\Charge;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Subscription as StripeSubscription;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController extends AbstractController
{
    private StripeApi $stripeApi;
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;

    private StripePaymentFactory $paymentFactory;

    public function __construct(
        StripeApi $stripeApi,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em,
        StripePaymentFactory $paymentFactory
    ) {
        $this->stripeApi = $stripeApi;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * @Route("/stripe/webhook", name="stripe_webhook")
     */
    public function index(Event $event): JsonResponse
    {
        switch ($event->type) {
            case 'payment_intent.succeeded':
                return $this->onPaymentIntentSucceeded($event->data['object']);
            case 'charge.refunded':
                return $this->onRefund($event->data['object']);
            case 'customer.subscription.created':
                return $this->onSubscriptionCreated($event->data['object']);
            case 'customer.subscription.updated':
                return $this->onSubscriptionUpdated($event->data['object']);
            case 'customer.subscription.deleted':
                return $this->onSubscriptionDeleted($event->data['object']);
            default:
                return $this->json([]);
        }
    }

    public function onPaymentIntentSucceeded(PaymentIntent $intent): JsonResponse
    {
        $user = $this->getUserFromCustomer((string) $intent->customer);
        $payment = $this->paymentFactory->createPaymentFromIntent($intent);
        $this->dispatcher->dispatch(new PaymentEvent($payment, $user));

        return new JsonResponse([]);
    }

    public function onRefund(Charge $charge): JsonResponse
    {
        $payment = new Payment();
        $payment->id = (string) $charge->payment_intent;
        $this->dispatcher->dispatch(new PaymentRefundedEvent($payment));

        return $this->json([]);
    }

    public function onSubscriptionCreated(StripeSubscription $stripeSubscription): JsonResponse
    {
        $plan = $this->em->getRepository(Plan::class)->find($stripeSubscription->metadata['plan_id']);
        if (null === $plan) {
            throw new NoResultException();
        }
        $subscription = (new Subscription())
            ->setState(Subscription::ACTIVE)
            ->setNextPayment(new \DateTimeImmutable("@{$stripeSubscription->current_period_end}"))
            ->setPlan($plan)
            ->setUser($this->getUserFromCustomer((string) $stripeSubscription->customer))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStripeId($stripeSubscription->id);
        $this->em->persist($subscription);
        $this->em->flush();

        return new JsonResponse([]);
    }

    private function onSubscriptionUpdated(StripeSubscription $stripeSubscription): JsonResponse
    {
        $subscription = $this->em->getRepository(Subscription::class)->findOneBy(['stripeId' => $stripeSubscription->id]);
        if (!($subscription instanceof Subscription)) {
            throw new \Exception("Impossible de trouver l'abonnement correspondant");
        }
        if ($stripeSubscription->cancel_at_period_end) {
            $subscription->setState(Subscription::INACTIVE);
        } else {
            $subscription->setState(Subscription::ACTIVE);
            $subscription->setNextPayment(new \DateTimeImmutable("@{$stripeSubscription->current_period_end}"));
        }
        $this->em->flush();

        return new JsonResponse([]);
    }

    private function onSubscriptionDeleted(StripeSubscription $stripeSubscription): JsonResponse
    {
        $subscription = $this->em->getRepository(Subscription::class)->findOneBy(['stripeId' => $stripeSubscription->id]);
        if (!($subscription instanceof Subscription)) {
            throw new \Exception("Impossible de trouver l'abonnement correspondant");
        }
        $this->em->remove($subscription);
        $this->em->flush();

        return new JsonResponse([]);
    }

    private function getUserFromCustomer(string $customerId): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['stripeId' => $customerId]);
        if (null === $user) {
            throw new \Exception("Impossible de trouver l'utilisateur correspondant au paiement");
        }

        return $user;
    }
}
