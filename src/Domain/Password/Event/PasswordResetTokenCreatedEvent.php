<?php

namespace App\Domain\Password\Event;

use App\Domain\Auth\User;
use App\Domain\Password\Entity\PasswordResetToken;

final class PasswordResetTokenCreatedEvent
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
