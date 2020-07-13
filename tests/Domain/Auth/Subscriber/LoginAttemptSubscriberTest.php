<?php

namespace App\Tests\Domain\Auth\Subscriber;

use App\Domain\Auth\Event\BadPasswordLoginEvent;
use App\Domain\Auth\Service\LoginAttemptService;
use App\Domain\Auth\Subscriber\LoginAttemptSubscriber;
use App\Domain\Auth\User;
use App\Tests\EventSubscriberTest;
use PHPUnit\Framework\MockObject\MockObject;

class LoginAttemptSubscriberTest extends EventSubscriberTest
{
    /**
     * @var MockObject|LoginAttemptService
     */
    private MockObject $service;

    public function testLogBadPasswordAttempt(): void
    {
        // On crée notre subscriber
        $subscriber = $this->getSubscriber();
        $event = $this->getEvent();

        $this->service->expects($this->once())
            ->method('addAttempt')
            ->with($event->getUser());

        $this->dispatch($subscriber, $event);
    }

    private function getSubscriber(): LoginAttemptSubscriber
    {
        /* @var MockObject|LoginAttemptService $service */
        $this->service = $this->getMockBuilder(LoginAttemptService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new LoginAttemptSubscriber($this->service);
    }

    private function getEvent(): BadPasswordLoginEvent
    {
        $user = new User();

        return new BadPasswordLoginEvent($user);
    }
}
