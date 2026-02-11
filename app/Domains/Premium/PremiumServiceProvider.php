<?php

namespace App\Domains\Premium;

use App\Domains\Premium\Subscriber\PaymentRefundedSubscriber;
use App\Domains\Premium\Subscriber\PaymentSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class PremiumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::subscribe(PaymentSubscriber::class);
        Event::subscribe(PaymentRefundedSubscriber::class);
    }
}
