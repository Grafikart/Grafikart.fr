<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import([
        'path' => '../src/Http/Controller/',
        'namespace' => 'App\Http\Controller',
    ], 'attribute');

    $routingConfigurator->import([
        'path' => '../src/Http/Admin/Controller/',
        'namespace' => 'App\Http\Admin\Controller',
    ], 'attribute')
        ->prefix('%admin_prefix%')
        ->namePrefix('admin_');

    $routingConfigurator->import([
        'path' => '../src/Http/Api/Controller/',
        'namespace' => 'App\Http\Api\Controller',
    ], 'attribute')
        ->prefix('/api')
        ->namePrefix('api_');

    $routingConfigurator->add('legacy_search', '/search')
        ->controller(RedirectController::class)
        ->defaults([
        'route' => 'search',
        'permanent' => true,
        'keepQueryParams' => true,
        'keepRequestMethod' => true,
    ]);
};
