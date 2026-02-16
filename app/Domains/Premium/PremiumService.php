<?php

namespace App\Domains\Premium;

use App\Domains\Premium\Models\Subscription;
use App\Models\User;

class PremiumService
{
    public function findSubscriptionForUser(User $user): ?Subscription
    {
        return Subscription::query()
            ->where('user_id', $user->id)
            ->orderByDesc('state')
            ->latest()
            ->first();
    }
}
