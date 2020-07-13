<?php

namespace App\Domain\Auth\Exception;

use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TooManyBadCredentialsException extends AuthenticationException
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct('Too many attempt.', 0, null);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMessageKey()
    {
        return 'Too many attempt.';
    }
}
