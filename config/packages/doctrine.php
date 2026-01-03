<?php

declare(strict_types=1);

use App\Infrastructure\Orm\Types\TsVector;
use DoctrineExtensions\Query\Mysql\Cast;
use DoctrineExtensions\Query\Mysql\Lpad;
use DoctrineExtensions\Query\Mysql\Now;
use DoctrineExtensions\Query\Mysql\Round;
use DoctrineExtensions\Query\Postgresql\DateFormat;
use DoctrineExtensions\Query\Postgresql\ExtractFunction;
use DoctrineExtensions\Query\Sqlite\Random;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
            'profiling_collect_backtrace' => '%kernel.debug%',
            'types' => [
                'tsvector' => TsVector::class,
            ],
        ],
        'orm' => [
            'validate_xml_mapping' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'mappings' => [
                'App\Domain' => [
                    'is_bundle' => false,
                    'type' => 'attribute',
                    'dir' => '%kernel.project_dir%/src/Domain',
                    'prefix' => 'App\Domain',
                    'alias' => 'Domain',
                ],
            ],
            'dql' => [
                'numeric_functions' => [
                    'RANDOM' => Random::class,
                    'ROUND' => Round::class,
                ],
                'datetime_functions' => [
                    'EXTRACT' => ExtractFunction::class,
                    'TO_CHAR' => DateFormat::class,
                    'NOW' => Now::class,
                ],
                'string_functions' => [
                    'LPAD' => Lpad::class,
                    'CAST' => Cast::class,
                ],
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
                'logging' => false,
                'url' => 'postgresql://test:test@dbtest:5432/test_%env(default:db_suffix:resolve:TEST_TOKEN)%?serverVersion=12&charset=utf8',
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'proxy_dir' => '%kernel.build_dir%/doctrine/orm/Proxies',
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
