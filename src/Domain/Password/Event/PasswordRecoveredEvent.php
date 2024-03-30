<?php

namespace App\Domain\Password\Event;

use App\Domain\Auth\User;

final readonly class PasswordRecoveredEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
