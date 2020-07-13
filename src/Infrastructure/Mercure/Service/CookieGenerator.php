<?php

namespace App\Infrastructure\Mercure\Service;

use App\Domain\Auth\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generate(User $user): Cookie
    {
        $token = (new Builder())
            ->withClaim('mercure', [
                'subscribe' => [
                    '/notifications/user/'.$user->getId(),
                    '/notifications/{channel}',
                ],
            ])
            ->getToken(new Sha256(), new Key($this->secret));

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
    }
}
