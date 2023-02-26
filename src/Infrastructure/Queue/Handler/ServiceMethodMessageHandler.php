<?php

namespace App\Infrastructure\Queue\Handler;

use App\Domain\Notification\NotificationService;
use App\Infrastructure\Mailing\Mailer;
use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use App\Infrastructure\Youtube\YoutubeUploaderService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

#[AsMessageHandler]
class ServiceMethodMessageHandler implements ServiceSubscriberInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function __invoke(ServiceMethodMessage $message): void
    {
        /** @var callable $callable */
        $callable = [
            $this->container->get($message->getServiceName()),
            $message->getMethod(),
        ];

        call_user_func_array($callable, $message->getParams());
    }

    public static function getSubscribedServices(): array
    {
        return [
            YoutubeUploaderService::class => YoutubeUploaderService::class,
            MailerInterface::class => MailerInterface::class,
            Mailer::class => Mailer::class,
            HubInterface::class => HubInterface::class,
            NotificationService::class => NotificationService::class,
        ];
    }
}
