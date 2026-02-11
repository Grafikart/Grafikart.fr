<?php

declare(strict_types=1);

namespace App\Domains\Premium\Subscriber;

use App\Domains\Premium\Event\PremiumCancelledEvent;
use App\Domains\Premium\Models\Transaction;
use App\Infrastructure\Payment\Event\PaymentRefundedEvent;

class PaymentRefundedSubscriber
{
    public function subscribe(): array
    {
        return [
            PaymentRefundedEvent::class => 'onPaymentRefunded',
        ];
    }

    public function onPaymentRefunded(PaymentRefundedEvent $event): void
    {
        $transaction = Transaction::where('method_id', $event->getPayment()->id)->first();
        if ($transaction === null || $transaction->isRefunded()) {
            return;
        }

        $user = $transaction->user;
        if ($user && $user->premium_end_at) {
            $user->premium_end_at = $user->premium_end_at->subMonths($transaction->duration);
            $user->save();
        }

        $transaction->update(['refunded_at' => now()]);

        if ($user) {
            event(new PremiumCancelledEvent($user));
        }
    }
}
