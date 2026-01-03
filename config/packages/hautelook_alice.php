<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ($containerConfigurator->env() === 'dev') {
        $containerConfigurator->extension('hautelook_alice', [
            'fixtures_path' => 'fixtures',
        ]);
    }
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('hautelook_alice', [
            'fixtures_path' => 'fixtures',
        ]);
    }
};
