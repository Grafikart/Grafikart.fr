<?php

namespace App\Infrastructure\Payment\Stripe;

use App\Infrastructure\Payment\Payment;
use Stripe\Charge;
use Stripe\PaymentIntent;

readonly class StripePaymentFactory
{
    public function __construct(private StripeApi $api) {}

    public function createPaymentFromIntent(PaymentIntent $intent): Payment
    {
        $charge = $intent->latest_charge;
        assert($charge instanceof Charge, 'Cannot resolve the charge from the payment intent');
        if (is_string($charge->balance_transaction)) {
            $charge->balance_transaction = $this->api->getTransaction($charge->balance_transaction);
        }
        // TODO : Check if it still exists in the API
        if ($intent->invoice) {
            $invoice = $this->api->getInvoice($intent->invoice);
            $subscription = $this->api->getSubscription((string) $invoice->subscription);
            $intent->metadata = $subscription->metadata;

            return new StripePayment($intent, $invoice);
        }

        // Le paiement provient d'une checkout session
        $session = $this->api->getCheckoutSessionFromIntent($intent->id);

        return new StripePayment($intent, $session);
    }
}
