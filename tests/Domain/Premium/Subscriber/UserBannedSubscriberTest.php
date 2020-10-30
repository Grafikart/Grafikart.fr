<?php

namespace App\Tests\Domain\Premium\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\User;
use App\Domain\Premium\Exception\PremiumNotBanException;
use App\Domain\Premium\Subscriber\UserBannedSubscriber;
use App\Tests\EventSubscriberTest;

class UserBannedSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(UserBannedSubscriber::class, UserBannedEvent::class);
    }

    public function testEventTriggersTheRightThing()
    {
        $user = (new User())->setPremiumEnd(new \DateTimeImmutable('+10 days'));
        $event = new UserBannedEvent($user);
        $this->expectException(PremiumNotBanException::class);
        $this->dispatch(new UserBannedSubscriber(), $event);
    }
}
