<?php

namespace App\Infrastructure\Mailing;

use App\Domains\Account\Events\UserDeletedEvent;
use App\Domains\Support\Event\SupportQuestionAnswered;
use App\Domains\Support\SupportQuestion;
use App\Infrastructure\Mailing\Mail\SupportQuestionAnsweredMail;
use App\Infrastructure\Mailing\Mail\UserDeletedMail;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;

class MailingSubscriber
{
    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            UserDeletedEvent::class => 'handleUserDeleted',
            SupportQuestionAnswered::class => 'handleSupportQuestionAnswered',
        ];
    }

    public function handleUserDeleted(UserDeletedEvent $event): void
    {
        if (empty($event->reason)) {
            return;
        }

        Mail::to(config('mail.from.address'))->send(new UserDeletedMail($event->user, $event->reason));
    }

    public function handleSupportQuestionAnswered(SupportQuestionAnswered $event): void
    {
        $question = $event->question->loadMissing([
            'course:id,title,slug',
            'user:id,email',
        ]);
        assert($question instanceof SupportQuestion);

        Mail::to($question->user->email)->send(new SupportQuestionAnsweredMail(
            question: $question,
            url: app_url($question->course, absolute: true).'#support',
        ));
    }
}
