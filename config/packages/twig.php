<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'file_name_pattern' => '*.twig',
        'default_path' => '%kernel.project_dir%/templates',
        'form_themes' => [
            'form/layout.html.twig',
        ],
        'date' => [
            'format' => 'd F Y',
            'interval_format' => '%%d days',
        ],
        'globals' => [
            'MERCURE_PUBLIC_URL' => '%env(resolve:MERCURE_PUBLIC_URL)%',
        ],
        'paths' => [
            '%kernel.project_dir%/src/Infrastructure/Maker/templates' => 'maker',
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
