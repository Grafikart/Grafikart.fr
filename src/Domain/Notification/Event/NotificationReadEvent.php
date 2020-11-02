<?php

namespace App\Domain\Notification\Event;

use App\Domain\Auth\User;

class NotificationReadEvent
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
