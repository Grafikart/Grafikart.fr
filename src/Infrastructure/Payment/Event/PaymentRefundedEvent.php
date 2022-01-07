<?php

namespace App\Infrastructure\Payment\Event;

use App\Infrastructure\Payment\Payment;

class PaymentRefundedEvent
{
    public function __construct(private readonly Payment $payment)
    {
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
