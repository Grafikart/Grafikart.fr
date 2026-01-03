<?php

declare(strict_types=1);

use Pentatrion\ViteBundle\Controller\ProfilerController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    if ($routingConfigurator->env() === 'dev') {
        $routingConfigurator->import('@PentatrionViteBundle/Resources/config/routing.yaml')
        ->prefix('/build');
        $routingConfigurator->add('_profiler_vite', '/_profiler/vite')
        ->controller([
            ProfilerController::class,
            'info',
        ]);
    }
};
