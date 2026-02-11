<?php

namespace App\Domains\Premium\Event;

use App\Models\User;

final readonly class PremiumSubscriptionEvent
{
    public function __construct(public User $user) {}
}
