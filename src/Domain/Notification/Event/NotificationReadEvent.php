<?php

namespace App\Domain\Notification\Event;

use App\Domain\Auth\User;

class NotificationReadEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
