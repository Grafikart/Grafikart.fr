<?php

declare(strict_types=1);

use App\Infrastructure\Storage\Naming\IdDirectoryNamer;
use App\Infrastructure\Uploader\PropertyGroupedDirectoryNamer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Vich\UploaderBundle\Naming\CurrentDateTimeDirectoryNamer;
use Vich\UploaderBundle\Naming\PropertyNamer;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('vich_uploader', [
        'db_driver' => 'orm',
        'mappings' => [
            'attachments' => [
                'uri_prefix' => '/uploads/attachments',
                'upload_destination' => '%kernel.project_dir%/public/uploads/attachments',
                'namer' => SmartUniqueNamer::class,
                'directory_namer' => [
                    'service' => CurrentDateTimeDirectoryNamer::class,
                    'options' => [
                        'date_time_format' => 'Y',
                        'date_time_property' => 'createdAt',
                    ],
                ],
                'inject_on_load' => false,
                'delete_on_update' => true,
                'delete_on_remove' => true,
            ],
            'lives' => [
                'uri_prefix' => '/uploads/lives',
                'upload_destination' => '%kernel.project_dir%/public/uploads/lives',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'youtubeId',
                    ],
                ],
                'inject_on_load' => false,
                'delete_on_update' => true,
                'delete_on_remove' => true,
            ],
            'sources' => [
                'upload_destination' => '%kernel.project_dir%/downloads/sources',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'slug',
                    ],
                ],
                'directory_namer' => [
                    'service' => IdDirectoryNamer::class,
                ],
                'inject_on_load' => false,
                'delete_on_update' => true,
                'delete_on_remove' => true,
            ],
            'podcast' => [
                'upload_destination' => '%kernel.project_dir%/downloads/podcasts',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'id',
                    ],
                ],
                'directory_namer' => [
                    'service' => IdDirectoryNamer::class,
                ],
                'inject_on_load' => false,
                'delete_on_update' => true,
                'delete_on_remove' => true,
            ],
            'icons' => [
                'upload_destination' => '%kernel.project_dir%/public/uploads/icons',
                'uri_prefix' => '/uploads/icons',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'slug',
                    ],
                ],
            ],
            'badges' => [
                'upload_destination' => '%kernel.project_dir%/public/uploads/badges',
                'uri_prefix' => '/uploads/badges',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'imageName',
                    ],
                ],
            ],
            'avatars' => [
                'uri_prefix' => '/uploads/avatars',
                'upload_destination' => '%kernel.project_dir%/public/uploads/avatars',
                'namer' => [
                    'service' => PropertyNamer::class,
                    'options' => [
                        'property' => 'id',
                    ],
                ],
                'directory_namer' => [
                    'service' => PropertyGroupedDirectoryNamer::class,
                    'options' => [
                        'property' => 'id',
                        'modulo' => 10000,
                    ],
                ],
                'inject_on_load' => false,
                'delete_on_update' => true,
                'delete_on_remove' => true,
            ],
        ],
    ]);
};
