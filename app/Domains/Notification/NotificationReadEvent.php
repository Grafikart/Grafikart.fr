<?php

namespace App\Domains\Notification;

final readonly class NotificationReadEvent
{
    public function __construct(public \App\Models\User $user) {}
}
