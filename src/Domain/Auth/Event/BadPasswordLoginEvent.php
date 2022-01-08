<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\User;

class BadPasswordLoginEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
