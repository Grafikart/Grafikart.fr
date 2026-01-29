<?php

return [
    App\Providers\AppServiceProvider::class,
    \App\Infrastructure\Blade\BladeServiceProvider::class,
    \App\Infrastructure\Search\SearchServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];
