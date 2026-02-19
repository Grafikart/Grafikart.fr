<?php

namespace App\Domains\Account\Events;

use App\Models\User;

final readonly class UserDeletedEvent
{
    public function __construct(public User $user, public ?string $reason = null) {}

}
