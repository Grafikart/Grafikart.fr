<?php

namespace App\Infrastructure\Payment;

class Payment
{

    public int $planId;

    public string $paymentId;

    public float $amount;

    public string $fullName = '';

    public string $address = '';

    public string $city = '';

    public string $postalCode = '';

    public string $countryCode = 'FR';

    public float $vat = 0;

}
