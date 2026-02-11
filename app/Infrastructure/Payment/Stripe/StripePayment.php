<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Infrastructure\Payment\Payment;
use Stripe\BalanceTransaction;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Stripe\InvoiceLineItem;
use Stripe\PaymentIntent;

class StripePayment extends Payment
{
    /**
     * @param  Session|Invoice  $extra  Extra data required to extract the amount / tax
     */
    public function __construct(PaymentIntent $intent, object $extra)
    {
        /** @var Charge $charge */
        $charge = $intent->charges->data[0];
        /** @var BalanceTransaction $transaction */
        $transaction = $charge->balance_transaction;

        $amount = 0;
        $vat = 0;
        if ($extra instanceof Invoice) {
            /** @var InvoiceLineItem $line */
            $line = $extra->lines->data[0];
            $amount = $line->amount;
            if (isset($line->tax_amounts[0])) {
                /** @var \stdClass $tax */
                $tax = $line->tax_amounts[0];
                $vat = $tax->amount;
            }
        } elseif ($extra instanceof Session) {
            $amount = $extra->amount_subtotal;
            $vat = ($extra->total_details['amount_tax'] ?? 0);
        }

        parent::__construct(
            id: $intent->id,
            amount: $amount,
            vat: $vat,
            fee: $transaction?->fee ?? 0,
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
