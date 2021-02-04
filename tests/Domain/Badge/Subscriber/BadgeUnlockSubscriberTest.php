<?php

namespace App\Tests\Domain\Badge\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Subscriber\BadgeUnlockSubscriber;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Domain\Revision\Event\RevisionAcceptedEvent;
use App\Domain\Revision\Revision;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;

class BadgeUnlockSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(BadgeUnlockSubscriber::class, PremiumSubscriptionEvent::class);
        $this->assertSubscribeTo(BadgeUnlockSubscriber::class, RevisionAcceptedEvent::class);
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

    public function testRevisionUnlocksABadge()
    {
        $user = new User();
        $revision = new Revision();
        $revision->setAuthor($user);
        $event = new RevisionAcceptedEvent($revision);
        $service = $this->createMock(BadgeService::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $subscriber = new BadgeUnlockSubscriber($service, $em);
        $service->expects($this->once())->method('unlock')->with($user, 'revision');
        $this->dispatch($subscriber, $event);
    }
}
