<?php

declare(strict_types=1);

namespace App\Domain\Auth\Subscriber;

use App\Domain\Auth\Repository\LoginAttemptRepository;
use App\Domain\Password\Event\PasswordRecoveredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResetSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoginAttemptRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordRecoveredEvent::class => 'onPasswordRecovered',
        ];
    }

    public function onPasswordRecovered(PasswordRecoveredEvent $event): void
    {
        $this->repository->deleteAttemptsFor($event->getUser());
    }
}
