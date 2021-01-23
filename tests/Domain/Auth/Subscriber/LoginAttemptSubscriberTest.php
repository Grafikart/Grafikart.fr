<?php

namespace App\Tests\Domain\Auth\Subscriber;

use App\Domain\Auth\Event\BadPasswordLoginEvent;
use App\Domain\Auth\Service\LoginAttemptService;
use App\Domain\Auth\Subscriber\LoginSubscriber;
use App\Domain\Auth\User;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
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

    private function getSubscriber(): LoginSubscriber
    {
        /* @var MockObject|LoginAttemptService $service */
        $this->service = $this->createMock(LoginAttemptService::class);
        $em = $this->createMock(EntityManagerInterface::class);

        return new LoginSubscriber($this->service, $em);
    }

    private function getEvent(): BadPasswordLoginEvent
    {
        $user = new User();

        return new BadPasswordLoginEvent($user);
    }
}
