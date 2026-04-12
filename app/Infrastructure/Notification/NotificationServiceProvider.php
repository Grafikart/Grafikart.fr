<?php

namespace App\Infrastructure\Notification;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(NotificationSubscriber::class);
    }
}
