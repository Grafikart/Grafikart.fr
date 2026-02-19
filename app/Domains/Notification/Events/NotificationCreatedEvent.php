<?php

namespace App\Domains\Notification\Events;

use App\Domains\Notification\Models\Notification;
use App\Domains\Notification\NotificationData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class NotificationCreatedEvent implements ShouldBroadcast
{
    public NotificationData $notification;

    public function __construct(Notification $notification)
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
