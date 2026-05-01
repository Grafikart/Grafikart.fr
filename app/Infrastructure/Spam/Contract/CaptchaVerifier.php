<?php

namespace App\Infrastructure\Spam\Contract;

interface CaptchaVerifier
{
    public function verify(string $key, string $ip): bool;
}
