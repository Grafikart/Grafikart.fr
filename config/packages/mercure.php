<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('mercure', [
        'hubs' => [
            'default' => [
                'url' => '%env(MERCURE_URL)%',
                'public_url' => '%env(MERCURE_PUBLIC_URL)%',
                'jwt' => [
                    'secret' => '%env(MERCURE_PUBLISHER_SECRET)%',
                    'publish' => '*',
                ],
            ],
        ],
    ]);
};
