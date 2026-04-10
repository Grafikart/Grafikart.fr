<?php

namespace App\Infrastructure\Mailing;

use App\Domains\Account\Events\UserDeletedEvent;
use App\Domains\Coupon\Coupon;
use App\Domains\Coupon\Event\CouponCreatedEvent;
use App\Domains\Support\Event\SupportQuestionAnswered;
use App\Infrastructure\Mailing\Notification\CouponCreatedNotification;
use App\Infrastructure\Mailing\Notification\SupportQuestionAnsweredNotification;
use App\Infrastructure\Mailing\Notification\UserDeletionNotification;
use App\Models\User;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class MailingSubscriber
{
    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            UserDeletedEvent::class => 'handleUserDeleted',
            CouponCreatedEvent::class => 'handleCouponCreated',
            SupportQuestionAnswered::class => 'handleSupportQuestionAnswered',
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
        $question = $event->question;
        $question->user->notify(new SupportQuestionAnsweredNotification(
            course: $question->course->title,
            url: app_url($question->course, absolute: true).'#support',
        ));
    }
}
