<?php

namespace App\Infrastructure\Spam;

use App\Infrastructure\Spam\Contract\CaptchaVerifier;
use Illuminate\Support\Facades\Http;

readonly class TurnstileVerifier implements CaptchaVerifier
{
    public function __construct(
        private string $secret
    ) {}

    public function verify(string $key, string $ip): bool
    {
        $response = Http::asJson()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $this->secret,
            'response' => $key,
            'remoteip' => $ip,
        ]);

        return boolval($response->json('success'));
    }
}
