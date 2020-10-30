<?php

declare(strict_types=1);

namespace App\Domain\Premium\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Premium\Exception\PremiumNotBanException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserBannedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserBannedEvent::class => 'onUserBanned',
        ];
    }

    public function onUserBanned(UserBannedEvent $event): void
    {
        if ($event->getUser()->isPremium()) {
            throw new PremiumNotBanException();
        }
    }
}
