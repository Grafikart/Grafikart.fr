<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\User;

class PasswordResetRequestEvent
{

    private User $user;
    private PasswordResetToken $token;

    public function __construct(User $user, PasswordResetToken $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }

}
