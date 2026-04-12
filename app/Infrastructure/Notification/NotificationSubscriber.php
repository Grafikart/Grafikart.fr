<?php

namespace App\Infrastructure\Notification;

use App\Domains\Account\Events\UserDeletedEvent;
use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Coupon\Coupon;
use App\Domains\Coupon\Event\CouponCreatedEvent;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Revision\Event\AcceptedRevisionEvent;
use App\Domains\Revision\Event\RejectedRevisionEvent;
use App\Domains\Support\Event\SupportQuestionAnswered;
use App\Infrastructure\Notification\Channel\Site;
use App\Infrastructure\Notification\Notification\ContentPublishedNotification;
use App\Infrastructure\Notification\Notification\CouponCreatedNotification;
use App\Infrastructure\Notification\Notification\RevisionAcceptedNotification;
use App\Infrastructure\Notification\Notification\RevisionRejectedNotification;
use App\Infrastructure\Notification\Notification\SupportQuestionAnsweredNotification;
use App\Infrastructure\Notification\Notification\SupportQuestionSiteNotification;
use App\Infrastructure\Notification\Notification\UserDeletionNotification;
use App\Models\User;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class NotificationSubscriber
{
    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            UserDeletedEvent::class => 'handleUserDeleted',
            CouponCreatedEvent::class => 'handleCouponCreated',
            AcceptedRevisionEvent::class => 'handleAcceptedRevision',
            RejectedRevisionEvent::class => 'handleRejectedRevision',
            SupportQuestionAnswered::class => 'handleSupportQuestionAnswered',
            ContentCreatedEvent::class => 'handleContentCreated',
            ContentUpdatedEvent::class => 'handleContentCreated',
        ];
    }

    public function handleUserDeleted(UserDeletedEvent $event): void
    {
        if (empty($event->reason)) {
            return;
        }

        User::findAdmin()->notify(new UserDeletionNotification($event->user, $event->reason));
    }

    public function handleCouponCreated(CouponCreatedEvent $event): void
    {
        $coupon = $event->coupon->loadMissing('school');
        assert($coupon instanceof Coupon);

        Notification::route('mail', $coupon->email)->notify(new CouponCreatedNotification(
            subject: $coupon->school->email_subject,
            message: $coupon->school->email_message,
            months: $coupon->months,
            code: $coupon->id,
        ));
    }

    public function handleSupportQuestionAnswered(SupportQuestionAnswered $event): void
    {
        $question = $event->question->loadMissing([
            'course:id,title,slug',
            'user:id',
        ]);
        $question->user->notify(new SupportQuestionAnsweredNotification(
            course: $question->course->title,
            url: app_url($question->course, absolute: true).'#support',
        ));
        $question->user->notify(new SupportQuestionSiteNotification($question));
    }

    public function handleAcceptedRevision(AcceptedRevisionEvent $event): void
    {
        $revision = $event->revision;

        if (! $revision->user) {
            return;
        }

        $revision->user->notify(new RevisionAcceptedNotification($revision));
    }

    public function handleRejectedRevision(RejectedRevisionEvent $event): void
    {
        $revision = $event->revision;

        if (! $revision->user) {
            return;
        }

        $revision->user->notify(new RevisionRejectedNotification($revision));
    }

    public function handleContentCreated(ContentCreatedEvent|ContentUpdatedEvent $event): void
    {
        $content = $event->content;
        if (
            ($content instanceof Course || $content instanceof Formation)
            && $content->wasChanged('online') && $content->online
            && $content->created_at->isFuture()
        ) {
            new Site()->notify(new ContentPublishedNotification($content));
        }
    }
}
