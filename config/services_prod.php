<?php

declare(strict_types=1);

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/domains/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(PayPalHttpClient::class)
        ->args([
        service('paypal_production_environment'),
    ]);
};
