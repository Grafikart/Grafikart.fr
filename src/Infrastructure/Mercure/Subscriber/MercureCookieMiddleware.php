<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Jwt\LcobucciFactory;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;

/**
 * Ajoute le cookie nécessaire à mercure sur les réponses.
 */
class MercureCookieMiddleware implements EventSubscriberInterface
{
    private readonly TokenFactoryInterface $tokenFactory;

    public function __construct(
        #[Autowire(env: "MERCURE_SUBSCRIBER_SECRET")]
        string                               $secret,
        private readonly NotificationService $notificationService,
        private readonly Security            $security,
        private readonly Authorization       $authorization,
    )
    {
        $this->tokenFactory = new LcobucciFactory($secret);
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
            fn(string $channel) => "/notifications/$channel",
            $this->notificationService->getChannelsForUser($user)
        );
        $exp = (new \DateTimeImmutable('+1 hour'));
        // On n'utilise pas la méthode createCookie d'authorization car elle utiliserait la clef secrète publisher
        // On part du clear cookie, pour le modifier et utiliser la clef d'abonnement.
        $cookie = $this->authorization->createClearCookie($request, null)
            ->withExpires(0)
            ->withValue($this->tokenFactory->create($channels, null, [
                // On est obligé de passer une date pour éviter les ms
                'exp' => new \DateTimeImmutable('@' . $exp->getTimestamp()),
            ]));
        $response->headers->setCookie($cookie);
    }

}
