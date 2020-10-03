<?php

namespace App\Domain\Premium\Event;

use App\Domain\Auth\User;

class PremiumSubscriptionEvent
{

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
