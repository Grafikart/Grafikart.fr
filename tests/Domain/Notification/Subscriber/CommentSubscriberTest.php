<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentCreatedEvent;
use App\Domain\Notification\NotificationService;
use App\Domain\Notification\Subscriber\CommentSubscriber;
use App\Tests\EventSubscriberTest;

class CommentSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(CommentSubscriber::class, CommentCreatedEvent::class);
    }

    public function testNoNotificationIfRootComment()
    {
        $user = new User();
        $comment = (new Comment())
            ->setAuthor($user)
            ->setContent('Bonjour les gens');
        $event = new CommentCreatedEvent($comment);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->never())->method('notifyUser');
        $subscriber = new CommentSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNotificationIfChildComment()
    {
        $user = new User();
        $user2 = new User();
        $comment = (new Comment())
            ->setAuthor($user)
            ->setContent('Bonjour les gens');
        $reply = (new Comment())
            ->setAuthor($user2)
            ->setContent('Bonjour les gens');
        $comment->addReply($reply);
        $event = new CommentCreatedEvent($reply);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->once())->method('notifyUser');
        $subscriber = new CommentSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testDoNotSendNotificationOnAuthor()
    {
        $user = new User();
        $comment = (new Comment())
            ->setAuthor($user)
            ->setContent('Bonjour les gens');
        $reply = (new Comment())
            ->setAuthor($user)
            ->setContent('Bonjour les gens');
        $comment->addReply($reply);
        $event = new CommentCreatedEvent($reply);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->never())->method('notifyUser');
        $subscriber = new CommentSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testSendNotificationsToEveryone()
    {
        $user = (new User())->setId(1);
        $user2 = (new User())->setId(2);
        $user3 = (new User())->setId(3);
        $comment = (new Comment())
            ->setAuthor($user)
            ->setContent('Bonjour les gens');
        $reply = (new Comment())
            ->setAuthor($user2)
            ->setContent('Bonjour les gens');
        $reply2 = (new Comment())
            ->setAuthor($user3)
            ->setContent('Bonjour les gens');
        $comment->addReply($reply);
        $comment->addReply($reply2);
        $event = new CommentCreatedEvent($reply2);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->exactly(2))->method('notifyUser');
        $subscriber = new CommentSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }
}
