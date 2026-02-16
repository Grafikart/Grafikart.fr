<?php

declare(strict_types=1);

namespace App\Http\API;

use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Subscription;
use App\Domains\Premium\Models\Transaction;
use App\Infrastructure\Payment\Event\PaymentEvent;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;
use App\Infrastructure\Payment\Payment;
use App\Infrastructure\Payment\Stripe\StripeApi;
use App\Infrastructure\Payment\Stripe\StripePaymentFactory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Event;
use Stripe\PaymentIntent;
use Stripe\Subscription as StripeSubscription;
use Stripe\SubscriptionItem;
use Stripe\Webhook;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class StripeWebhookController
{
    public function __construct(
        private StripePaymentFactory $paymentFactory,
        private StripeApi $api,
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        $event = $this->getEventFromRequest($request);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->onPaymentIntentSucceeded($event->data['object']);
                break;
            case 'charge.updated':
                $this->onChargeUpdated($event->data['object']);
                break;
            case 'charge.refunded':
                $this->onRefund($event->data['object']);
                break;
            case 'customer.subscription.created':
                $this->onSubscriptionCreated($event->data['object']);
                break;
            case 'customer.subscription.updated':
                $this->onSubscriptionUpdated($event->data['object']);
                break;
            case 'customer.subscription.deleted':
                $this->onSubscriptionDeleted($event->data['object']);
                break;
            default:
                break;
        }

        return response()->json();
    }

    private function getEventFromRequest(Request $request): Event
    {
        return Webhook::constructEvent(
            $request->getContent(false),
            (string) $request->headers->get('stripe-signature'),
            config('services.stripe.webhook_secret'),
        );
    }

    /**
     * Dispatch a Payment event when a payment succeeds
     */
    private function onPaymentIntentSucceeded(PaymentIntent $intent): void
    {
        $user = $this->getUserFromCustomer((string) $intent->customer);
        $payment = $this->paymentFactory->createPaymentFromIntent($intent);
        event(new PaymentEvent($payment, $user));
    }

    private function onRefund(Charge $charge): void
    {
        $payment = new Payment(
            id: (string) $charge->payment_intent,
            amount: $charge->amount,
        );
        $payment->id = (string) $charge->payment_intent;
        event(new PaymentRefundedEvent($payment));
    }

    private function onSubscriptionCreated(StripeSubscription $stripeSubscription): void
    {
        $plan = Plan::find($stripeSubscription->metadata['plan_id']);
        if ($plan === null) {
            throw new NotFoundHttpException;
        }
        $user = $this->getUserFromCustomer((string) $stripeSubscription->customer);
        $item = $stripeSubscription->items->first();
        assert($item instanceof SubscriptionItem);
        Subscription::create([
            'state' => 1,
            'next_payment' => new \DateTimeImmutable('@'.($item->current_period_end)),
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'stripe_id' => $stripeSubscription->id,
        ]);
    }

    private function onSubscriptionUpdated(StripeSubscription $stripeSubscription): void
    {
        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();
        if (! ($subscription instanceof Subscription)) {
            throw new \Exception("Impossible de trouver l'abonnement correspondant");
        }
        if ($stripeSubscription->cancel_at !== null) {
            $subscription->state = Subscription::INACTIVE;
        } else {
            $item = $stripeSubscription->items->first();
            assert($item instanceof SubscriptionItem);
            $subscription->state = Subscription::ACTIVE;
            $subscription->next_payment = new \DateTimeImmutable('@'.($item->current_period_end ?? 0));
        }
        $subscription->save();
    }

    private function onSubscriptionDeleted(StripeSubscription $stripeSubscription): void
    {
        Subscription::where('stripe_id', $stripeSubscription->id)->delete();
    }

    /**
     * Update the fee when the charge is updated
     */
    private function onChargeUpdated(Charge $charge): void
    {
        assert(is_string($charge->balance_transaction), "Cannot retrieve balance_transaction for charge {$charge->id}");
        $transaction = $this->api->getTransaction($charge->balance_transaction);
        Transaction::query()
            ->where(['transactions.method_id' => $charge->payment_intent])
            ->update([
                'fee' => $transaction->fee ?? 0,
            ]);
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
