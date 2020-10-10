<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Auth\User;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeApi
{
    private StripeClient $stripe;

    public function __construct(string $privateKey)
    {
        Stripe::setApiVersion('2020-08-27');
        $this->stripe = new StripeClient($privateKey);
    }

    /**
     * Crée un customer stripe et sauvegarde l'id dans l'utilisateur.
     */
    public function createCustomer(User $user): User
    {
        if ($user->getStripeId()) {
            return $user;
        }
        $client = $this->stripe->customers->create([
            'metadata' => [
                'user_id' => (string) $user->getId(),
            ],
            'email' => $user->getEmail(),
            'name' => $user->getUsername(),
        ]);
        $user->setStripeId($client->id);

        return $user;
    }

    public function getCustomer(string $customerId): Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    public function getSubscription(string $subscriptionId): Subscription
    {
        return $this->stripe->subscriptions->retrieve($subscriptionId);
    }

    public function getPaymentIntent(string $id): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($id);
    }

    /**
     * Crée une session et renvoie l'URL de paiement.
     */
    public function createSuscriptionSession(User $user): string
    {
        $session = $this->stripe->checkout->sessions->create([
            'cancel_url' => 'http://grafikart.localhost:8000/premium',
            'success_url' => 'http://grafikart.localhost:8000/premium',
            'mode' => 'subscription',
            'payment_method_types' => [
                'card',
            ],
            'customer' => $user->getStripeId(),
            'line_items' => [
                [
                    'price' => 'price_1HaIopFCMNgisvowydRrRRez',
                    'quantity' => 1,
                    'dynamic_tax_rates' => ['txr_1HadF1FCMNgisvow1UK5XVDi'],
                ],
            ],
        ]);

        return $session->id;
        /*
         * $session = $this->stripe->billingPortal->sessions->create([
         * 'customer' => $user->getStripeId(),
         * 'return_url' => 'http://grafikart.localhost:8000/premium',
         * ]);
         * **/
    }

    public function createPaymentSession(User $user, \App\Domain\Premium\Entity\Plan $plan): string
    {
        $session = $this->stripe->checkout->sessions->create([
            'cancel_url' => 'http://grafikart.localhost:8000/premium',
            'success_url' => 'http://grafikart.localhost:8000/premium',
            'mode' => 'payment',
            'payment_method_types' => [
                'card',
            ],
            'customer' => $user->getStripeId(),
            'metadata' => [
                'plan_id' => $plan->getId(),
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $plan->getName(),
                            'images' => ['https://www.grafikart.fr/assets/logo-footer-f0a333a1c7b2c1833354864ad7c405e0396d894732bf03abb902f2281e6c942e.png'],
                        ],
                        'unit_amount' => $plan->getPrice() * 100,
                    ],
                    'quantity' => 1,
                    // 'tax_rates' => ['txr_1HaHcHFCMNgisvowtytneRQe'],
                    'dynamic_tax_rates' => ['txr_1HadF1FCMNgisvow1UK5XVDi'],
                ],
            ],
        ]);

        return $session->id;
    }

    /**
     * Renvoie l'url du profil d'abonnement stripe.
     */
    public function getBillingUrl(User $user, string $returnUrl): string
    {
        return $this->stripe->billingPortal->sessions->create([
            'customer' => $user->getStripeId(),
            'return_url' => $returnUrl,
        ])->url;
    }

    public function getPlan(string $id): Plan
    {
        return $this->stripe->plans->retrieve($id);
    }
}
