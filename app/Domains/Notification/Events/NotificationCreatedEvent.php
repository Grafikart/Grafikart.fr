<?php

namespace App\Domains\Notification\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

readonly class NotificationCreatedEvent implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public \App\Domains\Notification\Models\Notification $notification) {}

    public function broadcastOn(): Channel
    {
        return new Channel('notification');
    }
}
