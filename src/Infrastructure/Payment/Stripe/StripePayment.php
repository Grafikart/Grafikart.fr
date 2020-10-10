<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Infrastructure\Payment\Payment;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class StripePayment extends Payment
{
    public function __construct(PaymentIntent $intent, Session $checkoutSession)
    {
        if (null === $checkoutSession->metadata) {
            throw new \RuntimeException("Impossible de récupérer l'id du plan");
        }
        $planId = $checkoutSession->metadata['plan_id'];
        /** @var Charge $charge */
        $charge = $intent->charges->data[0];
        $this->id = $intent->id;
        $this->planId = $planId;
        $this->firstname = $charge->billing_details['name'];
        $this->lastname = '';
        $this->address = $charge->billing_details['address']['line1']."\n".$charge->billing_details['address']['line2'];
        $this->city = $charge->billing_details['address']['city'];
        $this->postalCode = $charge->billing_details['address']['postal_code'];
        $this->countryCode = $charge->billing_details['address']['country'];
        $this->amount = $checkoutSession->amount_subtotal / 100;
        $this->vat = ($checkoutSession->total_details['amount_tax'] ?? 0) / 100;
    }
}
