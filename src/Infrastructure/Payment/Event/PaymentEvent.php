<?php

namespace App\Infrastructure\Payment\Event;

use App\Domain\Auth\User;
use App\Infrastructure\Payment\Payment;

class PaymentEvent
{
    private Payment $payment;
    private User $user;

    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
