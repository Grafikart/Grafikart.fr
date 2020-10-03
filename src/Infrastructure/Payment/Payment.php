<?php

namespace App\Infrastructure\Payment;

class Payment
{

    public string $id;

    public int $planId;

    public float $amount;

    public string $firstname = '';

    public string $lastname = '';

    public string $address = '';

    public string $city = '';

    public string $postalCode = '';

    public string $countryCode = 'FR';

    public float $vat = 0;
}
