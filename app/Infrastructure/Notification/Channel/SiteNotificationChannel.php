<?php

namespace App\Infrastructure\Notification\Channel;

use App\Infrastructure\Notification\Events\NotificationCreatedEvent;
use App\Infrastructure\Notification\SiteNotification;
use App\Models\User;

/**
 * Broadcast a notification on the site
 * - Create a new notification in the database
 * - Broadcast the notification using mercure
 */
class SiteNotificationChannel
{
    public function send(object $notifiable, HasSiteNotification $notification): void
    {
        $message = $notification->toSiteNotification($notifiable);
        $isUser = $notifiable instanceof User;
        $model = $message->target;

        // Persist the notification
        $item = SiteNotification::updateOrCreate([
            'url' => $message->url,
            'channel' => 'public',
            ...$model ? [
                'notifiable_id' => $model->getKey(),
                'notifiable_type' => $model->getMorphClass(),
            ] : [],
            ...$isUser ? [
                'user_id' => $notifiable->id,
            ] : [],
        ], [
            'message' => $message->message,
            'created_at' => now(),
        ]
        );

        // Emit the broadcastable event
        event(new NotificationCreatedEvent($item));
    }
}
