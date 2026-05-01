<?php

namespace App\Infrastructure\Spam;

class CaptchaRulesFactory
{
    public static function rules(): array
    {
        return match (config('captcha.driver')) {
            'turnstile' => [
                'cf-turnstile-response' => ['required', new CaptchaRule(new TurnstileVerifier(config('captcha.turnstile.secret')))],
            ],
            default => [],
        };
    }
}
