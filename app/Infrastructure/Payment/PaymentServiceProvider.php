<?php

namespace App\Infrastructure\Payment;

use App\Infrastructure\Payment\Stripe\StripeApi;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeApi::class, fn () => new StripeApi(
            privateKey: config('services.stripe.secret'),
        ));
    }
}
