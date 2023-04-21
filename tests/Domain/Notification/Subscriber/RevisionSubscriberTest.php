<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Notification\NotificationService;
use App\Domain\Notification\Subscriber\RevisionSubscriber;
use App\Domain\Revision\Event\RevisionAcceptedEvent;
use App\Domain\Revision\Event\RevisionRefusedEvent;
use App\Domain\Revision\Revision;
use App\Tests\EventSubscriberTest;

class RevisionSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(RevisionSubscriber::class, RevisionAcceptedEvent::class);
        $this->assertSubscribeTo(RevisionSubscriber::class, RevisionRefusedEvent::class);
    }

    public function testNoNotificationIfRevisionAccepted()
    {
        $revision = $this->getRevision();
        $event = new RevisionAcceptedEvent($revision);
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->once())->method('notifyUser');
        $subscriber = new RevisionSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function testNoNotificationIfRevisionRefused()
    {
        $revision = $this->getRevision();
        $event = new RevisionRefusedEvent($revision, "Erreur d'ortographe");
        $notification = $this->createMock(NotificationService::class);
        $notification->expects($this->once())->method('notifyUser');
        $subscriber = new RevisionSubscriber($notification);
        $this->dispatch($subscriber, $event);
    }

    public function getRevision(): Revision
    {
        $revision = (new Revision());
        $user = new User();
        $revision->setAuthor($user);
        $content = new Post();
        $revision->setTarget($content);

        return $revision;
    }
}
