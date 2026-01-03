<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('nelmio_cors', [
        'defaults' => [
            'origin_regex' => true,
            'allow_origin' => [
                '%env(CORS_ALLOW_ORIGIN)%',
            ],
            'allow_methods' => [
                'GET',
                'OPTIONS',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
            ],
            'allow_headers' => [
                'Content-Type',
                'Authorization',
            ],
            'expose_headers' => [
                'Link',
            ],
            'max_age' => 3600,
        ],
        'paths' => [
            '^/' => null,
        ],
    ]);
};
