<?php

namespace App\Infrastructure\Mercure\Service;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
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
        $token = (new Builder())
            ->withClaim('mercure', [
                'subscribe' => $channels,
            ])
            ->getToken(new Sha256(), new Key($this->secret));

        return Cookie::create('mercureAuthorization', $token, 0, '/.well-known/mercure');
    }
}
