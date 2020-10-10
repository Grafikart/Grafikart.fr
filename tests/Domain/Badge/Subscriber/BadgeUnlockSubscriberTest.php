<?php

namespace App\Tests\Domain\Badge\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Subscriber\BadgeUnlockSubscriber;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;

class BadgeUnlockSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(BadgeUnlockSubscriber::class, PremiumSubscriptionEvent::class);
    }

    public function testPremiumUnlocksABadge()
    {
        $user = new User();
        $event = new PremiumSubscriptionEvent($user);
        $service = $this->createMock(BadgeService::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $subscriber = new BadgeUnlockSubscriber($service, $em);
        $service->expects($this->once())->method('unlock')->with($user, 'premium');
        $this->dispatch($subscriber, $event);
    }
}
