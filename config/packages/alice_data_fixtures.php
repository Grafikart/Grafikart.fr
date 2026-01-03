<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('fidry_alice_data_fixtures', [
            'default_purge_mode' => 'no_purge',
        ]);
    }
};
