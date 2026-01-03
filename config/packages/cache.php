<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'cache' => [
            'app' => 'cache.adapter.array',
            'prefix_seed' => 'grafikart.fr',
            'default_redis_provider' => '%env(resolve:REDIS_URL)%',
            'pools' => [
                'view_cache_pool' => [
                    'default_lifetime' => '7 days',
                ],
            ],
        ],
    ]);

    $services = $containerConfigurator->services();

    $services->set('app.cache.adapter.redis')
        ->parent('cache.adapter.redis')
        ->arg('$redis', service(Redis::class))
        ->arg('$defaultLifetime', 604800)
        ->tag('cache.pool', [
        'namespace' => '%env(resolve:REDIS_POOL)%',
    ]);
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('framework', [
            'cache' => [
                'app' => 'app.cache.adapter.redis',
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('framework', [
            'cache' => [
                'app' => 'cache.adapter.array',
            ],
        ]);
    }
};
