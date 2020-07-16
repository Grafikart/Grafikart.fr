<?php

namespace App\Domain\Auth\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Throwable;

class UserBannedException extends CustomUserMessageAuthenticationException
{
    public function __construct($message = 'Ce compte a été bloqué', array $messageData = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
