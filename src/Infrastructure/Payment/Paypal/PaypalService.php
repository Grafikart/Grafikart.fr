<?php

namespace App\Infrastructure\Payment\Paypal;

use App\Infrastructure\Payment\Exception\PaymentFailedException;
use App\Infrastructure\Payment\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpException;

class PaypalService
{
    private PayPalHttpClient $client;

    public function __construct(PayPalHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * On crée un paiement à partir de l'id de la commande.
     */
    public function createPayment(string $orderId): Payment
    {
        try {
            // On récupère les information de la commande
            /** @var \stdClass $order */
            $order = $this->client->execute(new OrdersGetRequest($orderId))->result;
            // On normalise le paiement
            $payment = new Payment();
            $unit = $order->purchase_units[0];
            $payment->id = $order->id;
            $payment->planId = (int) $unit->custom_id;
            $payment->firstname = $order->payer->name->given_name;
            $payment->lastname = $order->payer->name->surname;
            $payment->address = $unit->shipping->address->address_line_1;
            $payment->city = $unit->shipping->address->admin_area_2;
            $payment->postalCode = $unit->shipping->address->postal_code;
            $payment->countryCode = $unit->shipping->address->country_code;
            $payment->amount = floatval($unit->amount->value);
            $payment->vat = floatval($unit->amount->breakdown->tax_total->value);

            return $payment;
        } catch (HttpException $e) {
            throw PaymentFailedException::fromPaypalHttpException($e);
        }
    }

    /**
     * Lance la "capture" du paiement.
     */
    public function capture(Payment $payment): Payment
    {
        try {
            /** @var \stdClass $capture */
            $capture = $this->client->execute(new OrdersCaptureRequest($payment->id))->result;
            if ('COMPLETED' === $capture->status) {
                $capture = $capture->purchase_units[0]->payments->captures[0];
                $payment->id = $capture->id;
                $payment->fee = $capture->seller_receivable_breakdown->paypal_fee->value;

                return $payment;
            }
            throw new PaymentFailedException('Impossible de capturer ce paiement');
        } catch (HttpException $e) {
            throw PaymentFailedException::fromPaypalHttpException($e);
        }
    }
}
