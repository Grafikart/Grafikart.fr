<?php

namespace App\Infrastructure\Payment\Paypal;

use App\Infrastructure\Payment\Payment;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Order;
use PaypalServerSdkLib\Models\OrdersCapture;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

class PaypalService
{
    private PaypalServerSdkClient $client;

    public function __construct()
    {
        $this->client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    config('services.paypal.id'),
                    config('services.paypal.secret')
                )
            )
            ->environment(config('app.env') === 'production' ? Environment::PRODUCTION : Environment::SANDBOX)
            ->build();
    }

    /**
     * Capture a payment
     */
    public function capture(string $orderId): Payment
    {
        $this->client->getOrdersController()
            ->captureOrder(['id' => $orderId])
            ->getResult();
        $order = $this->client->getOrdersController()
            ->getOrder(['id' => $orderId])
            ->getResult();
        assert($order instanceof Order);
        $unit = $order->getPurchaseUnits()[0];
        $item = $unit->getItems()[0];
        $payer = $order->getPayer();
        $address = $unit->getShipping()?->getAddress();
        $capture = $unit->getPayments()->getCaptures()[0];
        assert($capture instanceof OrdersCapture);
        if ($capture->getStatus() !== 'COMPLETED') {
            throw new \Exception('La capture du paiement a échouée, status : '.$capture->getStatus());
        }

        return new Payment(
            id: $order->getId(),
            amount: (int) round(floatval($unit->getAmount()->getValue()) * 100),
            vat: (int) round(floatval($item->getTax()?->getValue() ?? '0') * 100),
            fee: (int) round(floatval($capture->getSellerReceivableBreakdown()?->getPaypalFee()?->getValue() ?? '0') * 100),
            planId: (int) $unit->getCustomId(),
            firstname: $payer?->getName()?->getGivenName() ?? '',
            lastname: $payer?->getName()?->getSurname() ?? '',
            address: $address?->getAddressLine1() ?? '',
            city: $address?->getAdminArea2() ?? '',
            postalCode: $address?->getPostalCode() ?? '',
            countryCode: $address?->getCountryCode() ?? 'FR',
        );
    }
}
