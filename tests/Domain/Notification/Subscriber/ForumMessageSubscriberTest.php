<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Notification\NotificationService;
use App\Domain\Notification\Subscriber\ForumMessageSubscriber;
use App\Tests\EventSubscriberTest;

class ForumMessageSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(ForumMessageSubscriber::class, MessageCreatedEvent::class);
    }

    public function testNoNotificationIfAuthor()
    {
        $user = (new User())->setId(1);
        $message = (new Message())->setAuthor($user);
        $topic = (new Topic())->setAuthor($user)->addMessage($message);
        $event = new MessageCreatedEvent($message);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->never())->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNotifyAuthorOnMessage()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $message = (new Message())->setAuthor($user2);
        $topic = (new Topic())->setAuthor($user)->addMessage($message);
        $event = new MessageCreatedEvent($message);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->once())->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNoNotifyOnSpamMessage()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $message = (new Message())->setAuthor($user2)->setSpam(true);
        $topic = (new Topic())->setAuthor($user)->addMessage($message);
        $event = new MessageCreatedEvent($message);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->never())->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNotifyEveryParticipant()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $user3 = (new User())->setId(3);
        $message = (new Message())->setAuthor($user2);
        $message2 = (new Message())->setAuthor($user3);
        $topic = (new Topic())
            ->setAuthor($user)
            ->addMessage($message)
            ->addMessage($message2);
        $event = new MessageCreatedEvent($message2);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->exactly(2))->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNotifyEveryParticipantWhenMessagePostedByTopicOwner()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $user3 = (new User())->setId(3);
        $message = (new Message())->setAuthor($user2);
        $message2 = (new Message())->setAuthor($user3);
        $message3 = (new Message())->setAuthor($user);
        $topic = (new Topic())
            ->setAuthor($user)
            ->addMessage($message)
            ->addMessage($message2)
            ->addMessage($message3);
        $event = new MessageCreatedEvent($message3);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->exactly(2))->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNotifyEveryParticipantExceptNoNotification()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $user3 = (new User())->setId(3);
        $message = (new Message())->setAuthor($user2)->setNotification(false);
        $message2 = (new Message())->setAuthor($user3);
        $message3 = (new Message())->setAuthor($user);
        $topic = (new Topic())
            ->setAuthor($user)
            ->addMessage($message)
            ->addMessage($message2)
            ->addMessage($message3);
        $event = new MessageCreatedEvent($message3);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->exactly(1))->method('notifyUser');
        $subscriber = new ForumMessageSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }
}
