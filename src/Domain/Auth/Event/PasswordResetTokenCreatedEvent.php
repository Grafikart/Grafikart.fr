<?php

namespace App\Domain\Auth\Event;

use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\User;

class PasswordResetTokenCreatedEvent
{

    private PasswordResetToken $token;

    public function __construct(PasswordResetToken $token)
    {
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->token->getUser();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }

}
