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
        $charge = $intent->latest_charge;
        assert($charge instanceof Charge, 'Cannot resolve the charge from the payment intent '.$intent->id);
        $transaction = $charge->balance_transaction;
        assert($transaction instanceof BalanceTransaction, 'Cannot resolve the balance_transaction from the payment intent '.$intent->id);

        $amount = 0;
        $vat = 0;
        if ($extra instanceof Invoice) {
            $line = $extra->lines->data[0];
            assert($line instanceof InvoiceLineItem, 'Expecting a line when reading extra data for intent '.$intent->id);
            $amount = $line->amount;
            if (isset($line->tax_amounts[0])) {
                /** @var \stdClass $tax */
                $tax = $line->tax_amounts[0];
                assert($tax instanceof \stdClass, 'Expecting a tax_amount.0 to be a class on '.$intent->id);
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
