<?php

namespace App\Infrastructure\Notification\Events;

final readonly class NotificationReadEvent
{
    public function __construct(public \App\Models\User $user) {}
}
