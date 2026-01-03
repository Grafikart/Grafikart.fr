<?php

declare(strict_types=1);

use App\Infrastructure\Social\SocialLoginService;
use App\Tests\Infrastructure\Mercure\HubStub;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('admin_prefix', '%env(resolve:ADMIN_PREFIX)%');

    $parameters->set('download_path', '%kernel.project_dir%/tests/fixtures');

    $parameters->set('podcasts_path', '%download_path%');

    $parameters->set('videos_path', '%download_path%');

    $parameters->set('sources_path', '%download_path%');

    $parameters->set('db_suffix', '0');

    $containerConfigurator->import(__DIR__ . '/domains/');

    $services = $containerConfigurator->services();

    $services->set(HubStub::class)
        ->decorate('mercure.hub.default.traceable');

    $services->set(SocialLoginService::class)
        ->autowire(true)
        ->public();
};
