<?php

namespace App\Infrastructure\Mailing;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class MailingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(MailingSubscriber::class);
    }
}
