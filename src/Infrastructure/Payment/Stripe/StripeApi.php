<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Auth\User;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeApi
{
    public function __construct(string $privateKey)
    {
        Stripe::setApiKey($privateKey);
        Stripe::setApiVersion('2020-03-02');
    }

    public function createSubscription(string $paymentMethodId, User $user): \App\Domain\Premium\Entity\Subscription
    {
        // On crée le client sur stripe
        if ($user->getStripeId()) {
            $customer = new Customer($user->getStripeId());
        } else {
            $customer = Customer::create([
                'payment_method' => $paymentMethodId,
                'email' => $user->getEmail(),
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);
            $user->setStripeId($customer->id);
        }

        // On crée l'abonnement
        $stripeSubscription = Subscription::create([
            'customer' => $customer->id,
            'default_tax_rates' => ['txr_1GedVLFCMNgisvowEEJy4f7H'],
            'items' => [
                [
                    'plan' => 'premium-1month',
                ],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);
        $subscription = (new \App\Domain\Premium\Entity\Subscription())->setStripeId($stripeSubscription->id);

        return $subscription;
    }

    public function getPlan(string $planId): Plan
    {
        return Plan::retrieve($planId);
    }
}
