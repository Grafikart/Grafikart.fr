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
    private string $secret;
    private NotificationService $notificationService;

    public function __construct(string $secret, NotificationService $notificationService)
    {
        $this->secret = $secret;
        $this->notificationService = $notificationService;
    }

    public function generate(User $user): Cookie
    {
        $channels = array_map(fn (string $channel) => "/notifications/$channel", $this->notificationService->getChannelsForUser($user));
        $token = Configuration::forUnsecuredSigner()
            ->builder()
            ->withClaim('mercure', [
                'subscribe' => $channels,
            ])
            ->getToken(new Sha256(), InMemory::plainText($this->secret))
            ->toString();

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
    }
}
