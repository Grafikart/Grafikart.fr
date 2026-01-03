<?php

declare(strict_types=1);

use App\Domain\Course\Entity\Course;
use App\Domain\Course\EventListener\CourseDurationUpdater;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->bind('$videosPath', '%videos_path%');

    $services->set(CourseDurationUpdater::class)
        ->tag('doctrine.orm.property_change_listener', [
            'entity' => Course::class,
            'property' => 'videoPath',
            'method' => 'updateDuration',
        ]);
};
