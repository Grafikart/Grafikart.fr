<?php

namespace App\Infrastructure\Notification\Events;

use App\Infrastructure\Notification\NotificationData;
use App\Infrastructure\Notification\SiteNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class NotificationCreatedEvent implements ShouldBroadcast
{
    public NotificationData $notification;

    public function __construct(SiteNotification $notification)
    {
        $this->notification = NotificationData::from($notification);
    }

    public function broadcastOn(): Channel
    {
        if ($this->notification->userId) {
            return new Channel('notification/'.$this->notification->userId);
        }

        return new Channel('notification');
    }
}
