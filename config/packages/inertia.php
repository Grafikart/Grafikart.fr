<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('inertia', [
        'root_view' => 'inertia.html.twig',
        'ssr' => [
            'enabled' => false,
            'url' => 'http://127.0.0.1:13714/render',
        ],
    ]);
};
