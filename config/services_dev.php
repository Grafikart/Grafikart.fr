<?php

declare(strict_types=1);

use App\Http\Twig\TwigCacheExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/domains/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(TwigCacheExtension::class, TwigCacheExtension::class)
        ->arg('$active', false);
};
