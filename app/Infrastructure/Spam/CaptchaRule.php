<?php

namespace App\Infrastructure\Spam;

use App\Infrastructure\Spam\Contract\CaptchaVerifier;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

readonly class CaptchaRule implements ValidationRule
{
    public function __construct(private CaptchaVerifier $verifier) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! mb_check_encoding($value, 'UTF-8') || ! $this->verifier->verify($value, request()->ip())) {
            $fail('Captcha invalide');
        }
    }
}
