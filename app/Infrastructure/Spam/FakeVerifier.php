<?php

namespace App\Infrastructure\Spam;

use App\Infrastructure\Spam\Contract\CaptchaVerifier;

/**
 * Fake captcha verifier for testing
 */
class FakeVerifier implements CaptchaVerifier
{
    public function verify(string $key, string $ip): bool
    {
        return true;
    }
}
