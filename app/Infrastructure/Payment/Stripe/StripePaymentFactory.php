<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Infrastructure\Payment\Payment;
use Stripe\Charge;
use Stripe\PaymentIntent;

class StripePaymentFactory
{
    public function __construct(private readonly StripeApi $api) {}

    public function createPaymentFromIntent(PaymentIntent $intent): Payment
    {
        $intent = $this->api->getPaymentIntent($intent->id);
        $charge = $intent->latest_charge;
        assert($charge instanceof Charge, "Cannot read latest_charge for {$intent->id}");
        $details = $intent->amount_details;

        return new Payment(
            id: $intent->id,
            amount: $intent->amount,
            vat: $details['tax']['total_tax_amount'] ?? 0,
            fee: 0,
            planId: $intent->metadata['plan_id'],
            method: 'stripe',
            firstname: $charge->billing_details['name'] ?: '',
            address: $charge->billing_details['address']['line1']."\n".$charge->billing_details['address']['line2'],
            city: $charge->billing_details['address']['city'] ?: '',
            postalCode: $charge->billing_details['address']['postal_code'] ?: '',
            countryCode: $charge->billing_details['address']['country'] ?: '',
        );
    }
}
