<?php

declare(strict_types=1);

namespace App\Http\API;

use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Subscription;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Stripe\StripePaymentFactory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class StripeWebhookController
{
    public function __construct(
        private StripePaymentFactory $paymentFactory,
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        $event = $this->getEventFromRequest($request);

        return match ($event->type) {
            'payment_intent.succeeded' => $this->onPaymentIntentSucceeded($event->data['object']),
            'charge.refunded' => $this->onRefund($event->data['object']),
            'customer.subscription.created' => $this->onSubscriptionCreated($event->data['object']),
            'customer.subscription.updated' => $this->onSubscriptionUpdated($event->data['object']),
            'customer.subscription.deleted' => $this->onSubscriptionDeleted($event->data['object']),
            default => response()->json(),
        };
    }

    private function getEventFromRequest(Request $request): Event
    {
        return Webhook::constructEvent(
            $request->getContent(false),
            (string) $request->headers->get('stripe-signature'),
            config('services.stripe.webhook_secret'),
        );
    }

    private function onPaymentIntentSucceeded(PaymentIntent $intent): JsonResponse
    {
        $user = $this->getUserFromCustomer((string) $intent->customer);
        $payment = $this->paymentFactory->createPaymentFromIntent($intent);
        event(new PaymentEvent($payment, $user));

        return new JsonResponse([]);
    }

    private function onRefund(Charge $charge): JsonResponse
    {
        $payment = new Payment(
            id: (string) $charge->payment_intent,
            amount: $charge->amount,
        );
        $payment->id = (string) $charge->payment_intent;
        event(new PaymentRefundedEvent($payment));

        return response()->json([]);
    }

    private function onSubscriptionCreated(StripeSubscription $stripeSubscription): JsonResponse
    {
        $plan = Plan::find($stripeSubscription->metadata['plan_id']);
        if ($plan === null) {
            throw new NotFoundHttpException;
        }
        $user = $this->getUserFromCustomer((string) $stripeSubscription->customer);
        Subscription::create([
            'state' => 1,
            'next_payment' => new \DateTimeImmutable("@{$stripeSubscription->current_period_end}"),
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'stripe_id' => $stripeSubscription->id,
        ]);

        return new JsonResponse([]);
    }

    private function onSubscriptionUpdated(StripeSubscription $stripeSubscription): JsonResponse
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();
        if (! ($subscription instanceof Subscription)) {
            throw new \Exception("Impossible de trouver l'abonnement correspondant");
        }
        if ($stripeSubscription->cancel_at_period_end) {
            $subscription->state = Subscription::INACTIVE;
        } else {
            $subscription->state = Subscription::ACTIVE;
            $subscription->next_payment = new \DateTimeImmutable("@{$stripeSubscription->current_period_end}");
        }
        $subscription->save();

        return new JsonResponse([]);
    }

    private function onSubscriptionDeleted(StripeSubscription $stripeSubscription): JsonResponse
    {
        Subscription::where('stripe_id', $stripeSubscription->id)->delete();

        return new JsonResponse([]);
    }

    private function getUserFromCustomer(string $customerId): User
    {
        $user = User::where(['stripe_id' => $customerId])->first();
        if ($user === null) {
            throw new \Exception("Impossible de trouver l'utilisateur correspondant au paiement");
        }

        return $user;
    }
}
