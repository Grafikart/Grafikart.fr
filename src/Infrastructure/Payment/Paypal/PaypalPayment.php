<?php

namespace App\Infrastructure\Payment\Paypal;

use App\Infrastructure\Payment\Exception\PaymentFailedException;
use App\Infrastructure\Payment\Payment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Payments\CapturesGetRequest;
use PayPalHttp\HttpException;

class PaypalPayment
{

    private PayPalHttpClient $client;

    public function __construct(PayPalHttpClient $client)
    {
        $this->client = $client;
    }

    public function capture(string $key): Payment
    {
        try {
            // On récupère les information de la commaande
            /** @var \stdClass $order */
            $order = $this->client->execute(new OrdersGetRequest($key))->result;
            /** @var \stdClass $capture */
            $capture = $this->client->execute(new OrdersCaptureRequest($key))->result;

            // On normalise le paiement
            $payment = new Payment();
            $unit = $order->purchase_units[0];
            $payment->planId = (int)$unit->custom_id;
            $payment->paymentId = $capture->purchase_units[0]->payments->captures[0]->id;
            $payment->fullName =$unit->shipping->name->full_name;
            $payment->address = $unit->shipping->address->address_line_1;
            $payment->city =$unit->shipping->address->admin_area_2;
            $payment->postalCode =$unit->shipping->address->postal_code;
            $payment->countryCode =$unit->shipping->address->country_code;
            $payment->amount = floatval($unit->amount->breakdown->item_total->value);
            $payment->vat = floatval($unit->amount->breakdown->tax_total->value);

            // On capture le payement auprès de paypal
            if ($capture->status === 'COMPLETED') {
                return $payment;
            }
            throw new PaymentFailedException('Impossible de capturer ce paiement');
        } catch (HttpException $e) {
            throw PaymentFailedException::fromPaypalHttpException($e);
        }
    }

}
