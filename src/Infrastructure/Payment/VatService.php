<?php

namespace App\Infrastructure\Payment;

use App\Domain\Auth\User;

class VatService
{
    public function vat(?User $user): float
    {
        return null === $user || 'FR' === $user->getCountry() ? .2 : 0;
    }

    public function vatPrice(float $price, ?User $user): float
    {
        return floor($price * (1 + $this->vat($user)) * 100) / 100;
    }
}
