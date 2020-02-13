<?php

namespace App\Domain\Auth\Subscriber;

use App\Domain\Auth\Event\BadPasswordLoginEvent;
use App\Domain\Auth\LoginAttemptService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginAttemptSubscriber implements EventSubscriberInterface
{

    private LoginAttemptService $service;

    public function __construct(LoginAttemptService $service)
    {
        $this->service = $service;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BadPasswordLoginEvent::class => 'onAuthenticationFailure'
        ];
    }

    public function onAuthenticationFailure(BadPasswordLoginEvent $event): void
    {
        $this->service->addAttempt($event->getUser());
    }
}
