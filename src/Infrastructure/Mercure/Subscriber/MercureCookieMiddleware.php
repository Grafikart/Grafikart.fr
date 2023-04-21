<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Infrastructure\Mercure\Service\CookieGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ajoute le cookie nécessaire à mercure sur les réponses.
 */
class MercureCookieMiddleware implements EventSubscriberInterface
{
    public function __construct(private readonly CookieGenerator $generator, private readonly Security $security)
    {
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
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() ||
            !in_array('text/html', $request->getAcceptableContentTypes()) ||
            !($user = $this->security->getUser()) instanceof User
        ) {
            return;
        }
        $response->headers->setCookie($this->generator->generate($user));
    }
}
