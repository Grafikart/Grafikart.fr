<?php

declare(strict_types=1);

use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('oneup_flysystem', [
        'adapters' => [
            'local_adapter' => [
                'local' => [
                    'location' => '%kernel.project_dir%/public/uploads',
                ],
            ],
        ],
        'filesystems' => [
            'upload' => [
                'adapter' => 'local_adapter',
                'alias' => FilesystemOperator::class,
                'mount' => 'upload_fs',
            ],
        ],
    ]);
};
