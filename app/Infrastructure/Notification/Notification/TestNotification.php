<?php

namespace App\Infrastructure\Notification\Notification;

use App\Infrastructure\Notification\Channel\HasSiteNotification;
use App\Infrastructure\Notification\Channel\SiteNotificationChannel;
use App\Infrastructure\Notification\Channel\SiteNotificationMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements HasSiteNotification
{
    public function via(): string
    {
        return SiteNotificationChannel::class;
    }

    public function toSiteNotification(object $notifiable): SiteNotificationMessage
    {
        return new SiteNotificationMessage(
            url: '/',
            message: 'Ceci est une <strong>notification de test</strong>',
        );
    }
}
