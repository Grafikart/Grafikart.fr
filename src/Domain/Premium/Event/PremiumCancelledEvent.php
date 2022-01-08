<?php

namespace App\Domain\Premium\Event;

use App\Domain\Auth\User;

class PremiumCancelledEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
