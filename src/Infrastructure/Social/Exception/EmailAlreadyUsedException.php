<?php

namespace App\Infrastructure\Social\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class EmailAlreadyUsedException extends CustomUserMessageAuthenticationException
{
    public function __construct(
        string $message = 'Un compte existe déjà avec cet email. Pour associer votre compte facebook à ce compte, connectez vous et rendez vous sur votre profil.',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
