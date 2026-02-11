<?php

namespace App\Infrastructure\Payment;

/**
 * Represent a generic payment
 * All numbers are in cents
 */
class Payment
{
    public function __construct(
        public string $id,
        public int $amount,
        public int $vat = 0,
        public int $fee = 0,
        public ?int $planId = null,
        public string $method = '',
        public string $firstname = '',
        public string $lastname = '',
        public string $address = '',
        public string $city = '',
        public string $postalCode = '',
        public string $countryCode = 'FR',
    ) {}
}
