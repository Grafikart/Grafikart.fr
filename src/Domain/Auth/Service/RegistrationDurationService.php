<?php

namespace App\Domain\Auth\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Enregistre la durée du process d'inscription de l'utilisateur.
 */
class RegistrationDurationService
{
    private const SESSION_KEY = 'registration_request_time';

    public function startTimer(Request $request): void
    {
        $request->getSession()->set(self::SESSION_KEY, time());
        $request->getSession()->save();
    }

    public function getDuration(Request $request): int
    {
        $time = time();
        $start = $request->getSession()->get(self::SESSION_KEY) ?? $time;

        return $time - $start;
    }
}
