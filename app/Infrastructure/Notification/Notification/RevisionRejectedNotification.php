<?php

namespace App\Infrastructure\Notification\Notification;

use App\Domains\Revision\Revision;
use App\Infrastructure\Notification\Channel\HasSiteNotification;
use App\Infrastructure\Notification\Channel\SiteNotificationChannel;
use App\Infrastructure\Notification\Channel\SiteNotificationMessage;
use Illuminate\Notifications\Notification;

class RevisionRejectedNotification extends Notification implements HasSiteNotification
{
    public function __construct(
        private readonly Revision $revision,
    ) {}

    public function via(): string
    {
        return SiteNotificationChannel::class;
    }

    public function toSiteNotification(object $notifiable): SiteNotificationMessage
    {
        $title = $this->revision->revisionable?->title ?? 'un contenu';

        return new SiteNotificationMessage(
            url: route('revisions.index'),
            message: "Votre modification sur <strong>{$title}</strong> a été rejetée.",
        );
    }
}
