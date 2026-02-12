<?php

namespace App\Domains\Notification;

use App\Domains\Notification\Subscriber\ContentSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::subscribe(ContentSubscriber::class);
    }
}
