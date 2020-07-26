<?php

namespace App\Domain\Auth\Security;

use App\Domain\Auth\Exception\TooManyBadCredentialsException;
use App\Domain\Auth\Exception\UserBannedException;
use App\Domain\Auth\Service\LoginAttemptService;
use App\Domain\Auth\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Bloque l'authentification de l'utilisateur.
 */
class UserChecker implements UserCheckerInterface
{
    private LoginAttemptService $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;
    }

    /**
     * Vérifie que l'utilisateur a le droit de se connecter.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && $this->loginAttemptService->limitReachedFor($user)) {
            throw new TooManyBadCredentialsException();
        }

        return;
    }

    /**
     * Vérifie que l'utilisateur connecté a le droit de continuer.
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isBanned()) {
            throw new UserBannedException();
        }

        return;
    }
}
