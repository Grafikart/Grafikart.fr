<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine_migrations', [
        'migrations_paths' => [
            'DoctrineMigrations' => '%kernel.project_dir%/src/Infrastructure/Migrations',
        ],
        'enable_profiler' => false,
    ]);
};
