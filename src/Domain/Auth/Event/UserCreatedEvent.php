<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\User;

class UserCreatedEvent
{
    private User $user;
    private bool $usingOauth;

    public function __construct(User $user, bool $usingOauth = false)
    {
        $this->user = $user;
        $this->usingOauth = $usingOauth;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsingOauth(): bool
    {
        return $this->usingOauth;
    }
}
