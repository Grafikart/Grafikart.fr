<?php

namespace App\Tests\Domain\Forum\Subscriber;

use App\Domain\Auth\Event\UserBannedEvent;
use App\Domain\Auth\User;
use App\Domain\Forum\Repository\MessageRepository;
use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\Forum\Subscriber\ForumSubscriber;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;

class ForumSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToTheRightEvents()
    {
        $this->assertSubscribeTo(ForumSubscriber::class, UserBannedEvent::class);
    }

    public function testDeleteUserContent()
    {
        $messageRepository = $this->createMock(MessageRepository::class);
        $topicRepository = $this->createMock(TopicRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $user = new User();
        $event = new UserBannedEvent($user);
        $subscriber = new ForumSubscriber($topicRepository, $messageRepository, $em);
        $messageRepository->expects($this->once())->method('deleteForUser')->with($user);
        $topicRepository->expects($this->once())->method('deleteForUser')->with($user);
        $this->dispatch($subscriber, $event);
    }
}
