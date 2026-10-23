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
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeApi
{
    private readonly StripeClient $stripe;

    public function __construct(string $privateKey)
    {
        $this->stripe = new StripeClient([
            'api_key' => $privateKey,
            // La migration du SDK ne doit pas changer le contrat de l'API.
            'stripe_version' => '2026-01-28.clover',
        ]);
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

    public function getInvoiceFromIntent(string $paymentIntent): ?Invoice
    {
        $payments = $this->stripe->invoicePayments->all([
            'payment' => ['type' => 'payment_intent', 'payment_intent' => $paymentIntent],
            'expand' => ['data.invoice'],
            'limit' => 1,
        ]);

        return $payments->data[0]->invoice ?? null;
    }

    public function getSubscription(string $subscriptionId): Subscription
    {
        return $this->stripe->subscriptions->retrieve($subscriptionId);
    }

    public function getPaymentIntent(string $id): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($id, [
            'expand' => [
                'latest_charge.balance_transaction',
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
                    'plan_id' => (string) $plan->id,
                ],
            ],
            'metadata' => [
                'plan_id' => (string) $plan->id,
            ],
            'customer' => $user->stripe_id,
            'automatic_tax' => ['enabled' => true],
            'billing_address_collection' => 'required',
            'customer_update' => ['address' => 'auto'],
            'line_items' => [
                [
                    // Ce prix doit être configuré en TTC (tax_behavior=inclusive) dans Stripe.
                    'price' => $plan->stripe_id,
                    'quantity' => 1,
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
            'automatic_tax' => ['enabled' => true],
            'billing_address_collection' => 'required',
            'customer_update' => ['address' => 'auto'],
            'payment_method_types' => [
                'card',
            ],
            'customer' => $user->stripe_id,
            'metadata' => [
                'plan_id' => (string) $plan->id,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'plan_id' => (string) $plan->id,
                ],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'tax_behavior' => 'inclusive',
                        'product_data' => [
                            'name' => $plan->name,
                            'images' => ['https://www.grafikart.fr/assets/logo-footer-f0a333a1c7b2c1833354864ad7c405e0396d894732bf03abb902f2281e6c942e.png'],
                        ],
                        'unit_amount' => $plan->price * 100,
                    ],
                    'quantity' => 1,
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

    public function getCheckoutSessionFromIntent(string $paymentIntent): ?Session
    {
        /** @var Session[] $sessions */
        $sessions = $this->stripe->checkout->sessions->all(['payment_intent' => $paymentIntent])->data;

        return $sessions[0] ?? null;
    }

    public function getTransaction(string $id): BalanceTransaction
    {
        return $this->stripe->balanceTransactions->retrieve($id);
    }
}
