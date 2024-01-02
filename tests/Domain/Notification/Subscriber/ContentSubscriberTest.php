<?php

namespace App\Tests\Domain\Notification\Subscriber;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Notification\NotificationService;
use App\Domain\Notification\Subscriber\ContentSubscriber;
use App\Infrastructure\Queue\EnqueueMethod;
use PHPUnit\Framework\TestCase;

class ContentSubscriberTest extends TestCase
{
    public function testNotificationSentWhenCourseBecomeOnline()
    {
        $course = (new Course())
            ->setCreatedAt(new \DateTimeImmutable('- 1 hours'))
            ->setTitle('Titre de test');
        $previous = clone $course;
        $course->setOnline(true);
        $event = new ContentUpdatedEvent($course, $previous);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $notificationService = $this->getMockBuilder(NotificationService::class)->disableOriginalConstructor()->getMock();
        $notificationService->expects($this->once())->method('notifyChannel')->with('public', $this->stringContains($course->getTitle()), $course);
        (new ContentSubscriber($notificationService, $enqueuMethod))->onUpdate($event);
    }

    public function testNotificationIsNotSentWhenCourseDoesNotBecomeOnline()
    {
        $course = (new Course())
            ->setCreatedAt(new \DateTimeImmutable('- 1 hours'))
            ->setTitle('Titre de test')->setOnline(true);
        $previous = clone $course;
        $course->setTitle('Bonjour les gens');
        $event = new ContentUpdatedEvent($course, $previous);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $notificationService = $this->getMockBuilder(NotificationService::class)->disableOriginalConstructor()->getMock();
        $notificationService->expects($this->never())->method('notifyChannel');
        (new ContentSubscriber($notificationService, $enqueuMethod))->onUpdate($event);
    }

    public function testNotificationIsNotSentForFutureCourse()
    {
        $course = (new Course())
            ->setCreatedAt(new \DateTimeImmutable('+ 10 days'))
            ->setTitle('Titre de test');
        $previous = clone $course;
        $course->setOnline(true);
        $event = new ContentUpdatedEvent($course, $previous);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $enqueuMethod->expects($this->once())->method('enqueue');
        $notificationService = $this->getMockBuilder(NotificationService::class)->disableOriginalConstructor()->getMock();
        $notificationService->expects($this->never())->method('notifyChannel');
        (new ContentSubscriber($notificationService, $enqueuMethod))->onUpdate($event);
    }

    public function testNotificationSentWhenCourseIsCreated()
    {
        $course = (new Course())
            ->setOnline(true)
            ->setCreatedAt(new \DateTimeImmutable('- 1 hours'))
            ->setTitle('Titre de test');
        $event = new ContentCreatedEvent($course);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $notificationService = $this->createMock(NotificationService::class);
        $notificationService->expects($this->once())->method('notifyChannel')->with('public', $this->stringContains($course->getTitle()), $course);
        (new ContentSubscriber($notificationService, $enqueuMethod))->onCreate($event);
    }

    public function testNotificationNotSentWhenCourseIsOffline()
    {
        $course = (new Course())
            ->setOnline(false)
            ->setCreatedAt(new \DateTimeImmutable('- 1 hours'))
            ->setTitle('Titre de test');
        $event = new ContentCreatedEvent($course);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $notificationService = $this->createMock(NotificationService::class);
        $notificationService->expects($this->never())->method('notifyChannel');
        (new ContentSubscriber($notificationService, $enqueuMethod))->onCreate($event);
    }

    public function testNotificationNotSentWhenOldCourseIsOnline()
    {
        $course = (new Course())
            ->setOnline(false)
            ->setCreatedAt(new \DateTimeImmutable('- 3 days'))
            ->setTitle('Titre de test');
        $event = new ContentCreatedEvent($course);
        $enqueuMethod = $this->createMock(EnqueueMethod::class);
        $notificationService = $this->createMock(NotificationService::class);
        $notificationService->expects($this->never())->method('notifyChannel');
        (new ContentSubscriber($notificationService, $enqueuMethod))->onCreate($event);
    }
}
