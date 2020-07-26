<?php

namespace App\Infrastructure\Mercure\Subscriber;

use App\Domain\Auth\User;
use App\Infrastructure\Mercure\Service\CookieGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

/**
 * Ajoute le cookie nécessaire à mercure sur les réponses.
 */
class MercureCookieMiddleware implements EventSubscriberInterface
{
    private Security $security;
    private CookieGenerator $generator;

    public function __construct(CookieGenerator $generator, Security $security)
    {
        $this->security = $security;
        $this->generator = $generator;
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
            HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType() ||
            !in_array('text/html', $request->getAcceptableContentTypes()) ||
            !($user = $this->security->getUser()) instanceof User
        ) {
            return;
        }
        $response->headers->setCookie($this->generator->generate($user));
    }
}
