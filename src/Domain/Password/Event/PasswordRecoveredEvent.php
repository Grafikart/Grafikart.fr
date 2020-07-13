<?php

namespace App\Domain\Password\Event;

use App\Domain\Auth\User;

final class PasswordRecoveredEvent
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
