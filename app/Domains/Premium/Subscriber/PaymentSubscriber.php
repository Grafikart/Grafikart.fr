<?php

declare(strict_types=1);

namespace App\Domains\Premium\Subscriber;

use App\Domains\Premium\Event\PremiumSubscriptionEvent;
use App\Domains\Premium\Exception\PaymentPlanMissMatchException;
use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Transaction;
use App\Infrastructure\Payment\Event\PaymentEvent;

class PaymentSubscriber
{
    public function subscribe(): array
    {
        return [
            PaymentEvent::class => 'onPayment',
        ];
    }

    public function onPayment(PaymentEvent $event): void
    {
        $payment = $event->getPayment();
        $user = $event->getUser();
        $plan = Plan::where('price', (int) ($payment->amount / 100))->first();
        if ($plan === null) {
            throw new PaymentPlanMissMatchException;
        }

        Transaction::create([
            'user_id' => $user->id,
            'price' => $payment->amount,
            'tax' => $payment->vat,
            'duration' => $plan->duration,
            'method' => $payment->method,
            'method_id' => $payment->id,
            'firstname' => $payment->firstname,
            'lastname' => $payment->lastname,
            'city' => $payment->city,
            'address' => $payment->address,
            'postal_code' => $payment->postalCode,
            'country_code' => $payment->countryCode,
            'fee' => $payment->fee,
        ]);

        $premiumEnd = $user->premium_end_at?->isFuture() ? $user->premium_end_at : now();
        $user->premium_end_at = $premiumEnd->addMonths($plan->duration);
        $user->save();

        event(new PremiumSubscriptionEvent($user));
    }
}
