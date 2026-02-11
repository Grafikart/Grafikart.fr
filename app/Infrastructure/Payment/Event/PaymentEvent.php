<?php

namespace App\Infrastructure\Payment\Event;

use App\Infrastructure\Payment\Payment;
use App\Models\User;

readonly class PaymentEvent
{
    public function __construct(private Payment $payment, private User $user) {}

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
