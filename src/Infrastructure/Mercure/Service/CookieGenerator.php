<?php

namespace App\Infrastructure\Mercure\Service;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\Cookie;

class CookieGenerator
{
    public function __construct(private readonly string $secret, private readonly NotificationService $notificationService)
    {
    }

    public function generate(User $user): Cookie
    {
        $channels = array_map(
            fn (string $channel) => "/notifications/$channel",
            $this->notificationService->getChannelsForUser($user)
        );
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secret)
        );
        $token = $config->builder()
            ->withClaim('mercure', [
                'subscribe' => $channels,
            ])
            ->getToken($config->signer(), $config->signingKey())
            ->toString();

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
    }
}
