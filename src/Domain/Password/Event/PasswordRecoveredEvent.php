<?php

namespace App\Domain\Password\Event;

use App\Domain\Auth\User;

final class PasswordRecoveredEvent
{
    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
