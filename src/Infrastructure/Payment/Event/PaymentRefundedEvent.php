<?php

namespace App\Infrastructure\Payment\Event;

use App\Infrastructure\Payment\Payment;

class PaymentRefundedEvent
{
    private Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
