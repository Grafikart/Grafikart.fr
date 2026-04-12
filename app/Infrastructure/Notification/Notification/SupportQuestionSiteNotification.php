<?php

namespace App\Infrastructure\Notification\Notification;

use App\Domains\Support\SupportQuestion;
use App\Infrastructure\Notification\Channel\HasSiteNotification;
use App\Infrastructure\Notification\Channel\SiteNotificationChannel;
use App\Infrastructure\Notification\Channel\SiteNotificationMessage;
use Illuminate\Notifications\Notification;

class SupportQuestionSiteNotification extends Notification implements HasSiteNotification
{
    public function __construct(
        private readonly SupportQuestion $question,
    ) {}

    public function via(): string
    {
        return SiteNotificationChannel::class;
    }

    public function toSiteNotification(object $notifiable): SiteNotificationMessage
    {
        return new SiteNotificationMessage(
            url: app_url($this->question->course).'#support',
            message: "Votre question <strong>{$this->question->title}</strong> a reçu une réponse.",
        );
    }
}
