<?php

namespace App\Domain\Profile\Event;

use App\Domain\Auth\User;

class UserDeleteRequestEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
