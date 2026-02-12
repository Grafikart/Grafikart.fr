<?php

namespace App\Domains\Notification\Jobs;

use App\Domains\Notification\Events\NotificationCreatedEvent;
use App\Domains\Notification\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotificationBroadcasterJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Notification $notification) {}

    public function handle(): void
    {
        event(new NotificationCreatedEvent($this->notification));
    }
}
