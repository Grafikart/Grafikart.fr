<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Infrastructure\Payment\Payment;
use Stripe\BalanceTransaction;
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
        $balanceTransaction = $charge->balance_transaction;
        if (is_string($balanceTransaction)) {
            $balanceTransaction = $this->api->getTransaction($balanceTransaction);
        }
        assert($balanceTransaction instanceof BalanceTransaction);
        $fee = $balanceTransaction->fee;
        $invoice = $this->api->getInvoiceFromIntent($intent->id);
        if ($invoice !== null) {
            $vat = 0;
            foreach ($invoice->total_taxes ?? [] as $tax) {
                $vat += $tax->amount;
            }
            $metadata = $invoice->parent?->subscription_details?->metadata;
        } else {
            $session = $this->api->getCheckoutSessionFromIntent($intent->id);
            if ($session === null || $session->total_details?->amount_tax === null) {
                throw new \RuntimeException("Cannot retrieve Checkout tax for {$intent->id}");
            }
            $vat = $session->total_details->amount_tax;
            $metadata = $session->metadata;
        }
        $planId = $metadata['plan_id'] ?? $intent->metadata['plan_id'] ?? null;
        assert($planId !== null, "Cannot retrieve plan_id for {$intent->id}");
        $billing = $charge->billing_details;
        $address = $billing?->address;

        return new Payment(
            id: $intent->id,
            amount: $intent->amount,
            vat: $vat,
            fee: $fee,
            planId: $planId,
            method: 'stripe',
            firstname: $billing?->name ?? '',
            address: ($address?->line1 ?? '')."\n".($address?->line2 ?? ''),
            city: $address?->city ?? '',
            postalCode: $address?->postal_code ?? '',
            countryCode: $address?->country ?? '',
        );
    }
}
