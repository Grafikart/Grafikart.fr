<?php

namespace App\Infrastructure\Mailing;

use App\Domains\Account\Events\UserDeletedEvent;
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
        ];
    }

    public function handleUserDeleted(UserDeletedEvent $event): void
    {
        if (empty($event->reason)) {
            return;
        }

        Mail::to(config('mail.from.address'))->send(new UserDeletedMail($event->user, $event->reason));
    }
}
