<?php

declare(strict_types=1);

use App\Command\DumpCommand;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Helper\Paginator\KnpPaginator;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Admin\Firewall\AdminRequestListener;
use App\Http\Twig\TwigBreadcrumbExtension;
use App\Http\Twig\TwigUrlExtension;
use App\Infrastructure\Captcha\CaptchaImageService;
use App\Infrastructure\Captcha\HCaptcha\HCaptchaType;
use App\Infrastructure\Captcha\HCaptcha\IsValidHCaptchaValidator;
use App\Infrastructure\Image\ImageResizer;
use App\Infrastructure\Mailing\Mailer;
use App\Infrastructure\Queue\FailedJobsService;
use App\Infrastructure\Spam\SpamService;
use App\Infrastructure\Storage\DropboxFilesystemFactory;
use App\Infrastructure\Storage\Naming\IdDirectoryNamer;
use App\Infrastructure\Uploader\PropertyGroupedDirectoryNamer;
use App\Normalizer\Breadcrumb\BreadcrumbGeneratorInterface;
use Knp\Bundle\PaginatorBundle\Helper\Processor;
use League\Flysystem\Filesystem;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('admin_prefix', '%env(resolve:ADMIN_PREFIX)%');

    $parameters->set('download_path', '%kernel.project_dir%/downloads');

    $parameters->set('podcasts_path', '%kernel.project_dir%/public/podcasts');

    $parameters->set('videos_path', '%download_path%/videos');

    $parameters->set('sources_path', '%download_path%/sources');

    $parameters->set('image_resize_key', '%env(resolve:IMAGE_RESIZE_KEY)%');

    $parameters->set('geoip_database', '%kernel.project_dir%/var/GeoLite2-Country.mmdb');

    $parameters->set('dkim_key', '%env(resolve:default::MAILER_DKIM)%');

    $parameters->set('router.request_context.host', 'grafikart.fr');

    $parameters->set('router.request_context.scheme', 'https');

    $parameters->set('asset.request_context.secure', true);

    $parameters->set('captcha_id', '%env(resolve:default::CAPTCHA_ID)%');

    $parameters->set('captcha_secret', '%env(resolve:default::CAPTCHA_SECRET)%');

    $containerConfigurator->import(__DIR__ . '/domains/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$videosPath', '%videos_path%')
        ->bind('$projectDir', '%kernel.project_dir%')
        ->bind('$adminPrefix', '%admin_prefix%')
        ->bind('$appEnv', '%kernel.environment%');

    $services->instanceof(BreadcrumbGeneratorInterface::class)
        ->tag('app.breadcrumb');

    $services->set(Redis::class, Redis::class)
        ->lazy(true)
        ->call('connect', [
        '%env(REDIS_HOST)%',
    ]);

    $services->set(RedisSessionHandler::class)
        ->args([
        service(Redis::class),
    ]);

    $services->load('App\\', __DIR__ . '/../src/*')
        ->exclude([
        __DIR__ . '/../src/{DependencyInjection,Entity,Infrastructure/Migrations,Tests,Kernel.php}',
    ]);

    $services->load('App\Http\Controller\\', __DIR__ . '/../src/Http/Controller')
        ->tag('controller.service_arguments');

    $services->load('App\Http\Admin\Controller\\', __DIR__ . '/../src/Http/Admin/Controller')
        ->tag('controller.service_arguments');

    $services->load('App\Http\Api\Controller\\', __DIR__ . '/../src/Http/Api/Controller')
        ->tag('controller.service_arguments');

    $services->load('App\Component\ObjectMapper\Transform\\', __DIR__ . '/../src/Component/ObjectMapper/Transform')
        ->tag('objectMapper.transform')
        ->public();

    $services->set(IdDirectoryNamer::class)
        ->public();

    $services->set(AdminRequestListener::class)
        ->args([
        '%admin_prefix%',
    ]);

    $services->set(Processor::class);

    $services->set(PaginatorInterface::class, KnpPaginator::class);

    $services->set(TwigBreadcrumbExtension::class)
        ->args([
        tagged_iterator('app.breadcrumb'),
    ]);

    $services->set(PropertyGroupedDirectoryNamer::class)
        ->public();

    $services->set(ImageResizer::class)
        ->args([
        '%image_resize_key%',
    ]);

    $services->set(FailedJobsService::class)
        ->args([
        service('messenger.transport.failed'),
    ]);

    $services->set(SpamService::class)
        ->args([
        [
            Topic::class,
            Message::class,
        ],
    ]);

    $services->set(DumpCommand::class)
        ->arg('$dumpPath', '%kernel.project_dir%/var')
        ->arg('$filesystem', service('dropbox_filesystem'));

    $services->set(DropboxFilesystemFactory::class)
        ->args([
        '%env(DROPBOX_TOKEN)%',
    ]);

    $services->set(Mailer::class)
        ->arg('$dkimKey', '%dkim_key%');

    $services->set('dropbox_filesystem', Filesystem::class)
        ->factory([
        service(DropboxFilesystemFactory::class),
        'createFilesystem',
    ]);

    $services->set(HCaptchaType::class)
        ->arg('$apiKey', '%captcha_id%');

    $services->set(IsValidHCaptchaValidator::class)
        ->arg('$apiSecret', '%captcha_secret%');

    $services->set(CaptchaImageService::class)
        ->arg('$imagePath', '%kernel.project_dir%/public/images/captcha');

    $services->set(TwigUrlExtension::class)
        ->arg('$uploaderHelper', service(UploaderHelper::class));

    $services->alias(ContainerInterface::class, 'service_container');
};
