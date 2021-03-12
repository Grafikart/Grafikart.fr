<?php

namespace App\Tests\Domain\Auth\Subscriber;

use App\Domain\Auth\Repository\LoginAttemptRepository;
use App\Domain\Auth\Subscriber\PasswordResetSubscriber;
use App\Domain\Auth\User;
use App\Domain\Password\Event\PasswordRecoveredEvent;
use App\Tests\EventSubscriberTest;

class PasswordResetSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(PasswordResetSubscriber::class, PasswordRecoveredEvent::class);
    }

    public function testEventTriggersTheRightThing()
    {
        $user = new User();
        $event = new PasswordRecoveredEvent($user);
        $repository = $this->createMock(LoginAttemptRepository::class);
        $repository->expects($this->once())->method('deleteAttemptsFor')->with($user);
        $this->dispatch(new PasswordResetSubscriber($repository), $event);
    }
}
