<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Domains\Premium\Models\Plan;
use App\Models\User;
use Stripe\BalanceTransaction;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\Plan as StripePlan;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeApi
{
    private readonly StripeClient $stripe;

    private array $taxes = [];

    public function __construct(string $privateKey)
    {
        Stripe::setApiVersion('2026-01-28.clover');
        $this->taxes = ['txr_1SzcN4FLYwyu3L7pEz2Rnhlc'];
        if (str_contains($privateKey, 'live')) {
            $this->taxes = ['txr_1I7c7DFCMNgisvowdAol5zkl'];
        }
        $this->stripe = new StripeClient($privateKey);
    }

    /**
     * Create a stripe customer from a User
     */
    public function createCustomer(User $user): User
    {
        if ($user->stripe_id) {
            return $user;
        }
        $client = $this->stripe->customers->create([
            'metadata' => [
                'user_id' => (string) $user->id,
            ],
            'email' => $user->email,
            'name' => $user->name,
        ]);
        $user->stripe_id = $client->id;
        $user->save();

        return $user;
    }

    public function getCustomer(string $customerId): Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    public function getInvoice(string $invoice): Invoice
    {
        return $this->stripe->invoices->retrieve($invoice);
    }

    public function getSubscription(string $subscriptionId): Subscription
    {
        return $this->stripe->subscriptions->retrieve($subscriptionId);
    }

    public function getPaymentIntent(string $id): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($id, [
            'expand' => [
                'amount_details.line_items.data.tax',
                'latest_charge',
            ]]
        );
    }

    /**
     * Crée une session et renvoie l'URL de paiement.
     */
    public function createSuscriptionSession(User $user, Plan $plan, string $url): Session
    {
        return $this->stripe->checkout->sessions->create([
            'cancel_url' => $url,
            'success_url' => $url.'?success=1',
            'mode' => 'subscription',
            'payment_method_types' => [
                'card',
            ],
            'subscription_data' => [
                'metadata' => [
                    'plan_id' => $plan->id,
                ],
            ],
            'metadata' => [
                'plan_id' => $plan->id,
            ],
            'customer' => $user->stripe_id,
            'line_items' => [
                [
                    'price' => $plan->stripe_id,
                    'quantity' => 1,
                    'dynamic_tax_rates' => $this->taxes,
                ],
            ],
        ]);
    }

    public function createPaymentSession(User $user, Plan $plan, string $url): Session
    {
        return $this->stripe->checkout->sessions->create([
            'cancel_url' => $url,
            'success_url' => $url.'?success=1',
            'mode' => 'payment',
            'payment_method_types' => [
                'card',
            ],
            'customer' => $user->stripe_id,
            'metadata' => [
                'plan_id' => $plan->id,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'plan_id' => $plan->id,
                ],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $plan->name,
                            'images' => ['https://www.grafikart.fr/assets/logo-footer-f0a333a1c7b2c1833354864ad7c405e0396d894732bf03abb902f2281e6c942e.png'],
                        ],
                        'unit_amount' => $plan->price * 100,
                    ],
                    'quantity' => 1,
                    'dynamic_tax_rates' => $this->taxes,
                ],
            ],
        ]);
    }

    /**
     * Renvoie l'url du profil d'abonnement stripe.
     */
    public function getBillingUrl(User $user, string $returnUrl): string
    {
        return $this->stripe->billingPortal->sessions->create([
            'customer' => $user->stripe_id,
            'return_url' => $returnUrl,
        ])->url;
    }

    public function getPlan(string $id): StripePlan
    {
        return $this->stripe->plans->retrieve($id);
    }

    public function getCharge(string $id): Charge
    {
        return $this->stripe->charges->retrieve($id);
    }

    public function getCheckoutSessionFromIntent(string $paymentIntent): Session
    {
        /** @var Session[] $sessions */
        $sessions = $this->stripe->checkout->sessions->all(['payment_intent' => $paymentIntent])->data;

        return $sessions[0];
    }

    public function getTransaction(string $id): BalanceTransaction
    {
        return $this->stripe->balanceTransactions->retrieve($id);
    }
}
