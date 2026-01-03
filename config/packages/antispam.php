<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('antispam', [
        'profiles' => [
            'default' => [
                'timer' => [
                    'min' => 3,
                    'max' => 3600,
                ],
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('antispam', [
            'enabled' => false,
        ]);
    }
};
