<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(Google_Client::class, Google_Client::class)
        ->call('setClientId', [
        '%env(GOOGLE_ID)%',
    ])
        ->call('setClientSecret', [
        '%env(GOOGLE_SECRET)%',
    ]);
};
