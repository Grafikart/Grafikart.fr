<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mercure\Authorization;

/**
 * Ajoute le cookie nécessaire à mercure sur les réponses.
 */
class MercureCookieMiddleware implements EventSubscriberInterface
{
    public function __construct(
        private readonly Authorization $authorization,
        private readonly NotificationService $notificationService,
        private readonly Security $security,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['setMercureCookie'],
        ];
    }

    public function setMercureCookie(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        if (
            HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType()
            || !in_array('text/html', $request->getAcceptableContentTypes())
            || !($user = $this->security->getUser()) instanceof User
        ) {
            return;
        }
        $channels = array_map(
            fn (string $channel) => "/notifications/$channel",
            $this->notificationService->getChannelsForUser($user)
        );
        $exp = (new \DateTimeImmutable('+1 hour'));
        $cookie = $this->authorization->createCookie($request, $channels, [], [
            // We need to set a date without ms to avoid float in "exp"
            'exp' => new \DateTimeImmutable('@'.$exp->getTimestamp()),
        ]);
        $response->headers->setCookie($cookie);
    }
}
