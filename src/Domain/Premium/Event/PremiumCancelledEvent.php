<?php

namespace App\Domain\Premium\Event;

use App\Domain\Auth\User;

class PremiumCancelledEvent
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
