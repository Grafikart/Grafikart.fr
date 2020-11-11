<?php

namespace App\Tests\Infrastructure\Mercure\Subscriber;

use App\Domain\Badge\Event\BadgeUnlockEvent;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\Event\NotificationReadEvent;
use App\Infrastructure\Mercure\Subscriber\MercureSubscriber;
use App\Tests\EventSubscriberTest;

class MercureSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(MercureSubscriber::class, NotificationCreatedEvent::class);
        $this->assertSubscribeTo(MercureSubscriber::class, BadgeUnlockEvent::class);
        $this->assertSubscribeTo(MercureSubscriber::class, NotificationReadEvent::class);
    }
}
