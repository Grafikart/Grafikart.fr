<?php

namespace App\Domain\Password\Event;

use App\Domain\Auth\User;
use App\Domain\Password\Entity\PasswordResetToken;

final readonly class PasswordResetTokenCreatedEvent
{
    public function __construct(private PasswordResetToken $token)
    {
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
