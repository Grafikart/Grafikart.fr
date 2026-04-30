<?php

return [
    App\Infrastructure\Blade\BladeServiceProvider::class,
    App\Infrastructure\Payment\PaymentServiceProvider::class,
    App\Infrastructure\Notification\NotificationServiceProvider::class,
    App\Infrastructure\Search\SearchServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Domains\Account\AccountServiceProvider::class,
    App\Domains\Course\CourseServiceProvider::class,
    App\Domains\Premium\PremiumServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    \SimonSchaufi\LaravelDKIM\DKIMMailServiceProvider::class,
];
