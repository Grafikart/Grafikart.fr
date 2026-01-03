<?php

declare(strict_types=1);

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => [
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'retry_strategy' => [
                        'max_retries' => 0,
                        'delay' => 5000,
                    ],
                ],
                'failed' => 'doctrine://default',
                'sync' => 'sync://',
            ],
            'routing' => [
                ServiceMethodMessage::class => 'async',
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'dev') {
        $containerConfigurator->extension('framework', [
            'messenger' => [
                'routing' => [
                    ServiceMethodMessage::class => 'async',
                ],
            ],
        ]);
    }
};
