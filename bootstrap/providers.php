<?php

return [
    App\Infrastructure\Blade\BladeServiceProvider::class,
    App\Infrastructure\Payment\PaymentServiceProvider::class,
    App\Infrastructure\Search\SearchServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Domains\Course\CourseServiceProvider::class,
    App\Domains\Premium\PremiumServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];
