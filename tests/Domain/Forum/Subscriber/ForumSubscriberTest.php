<?php

namespace App\Tests\Domain\Forum\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\User;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\Forum\Subscriber\ForumSubscriber;
use App\Tests\EventSubscriberTest;

class ForumSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToTheRightEvents()
    {
        $this->assertSubsribeTo(ForumSubscriber::class, UserBannedEvent::class);
    }

    public function testDeleteUserContent()
    {
        $messageRepository = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $topicRepository = $this->getMockBuilder(TopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user = new User();
        $event = new UserBannedEvent($user);
        $subscriber = new ForumSubscriber($topicRepository, $messageRepository);
        $messageRepository->expects($this->once())->method('deleteForUser')->with($user);
        $topicRepository->expects($this->once())->method('deleteForUser')->with($user);
        $this->dispatch($subscriber, $event);
    }
}
