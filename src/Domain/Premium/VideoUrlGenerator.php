<?php

namespace App\Domain\Premium;

use App\Domain\Auth\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

/**
 * Génère l'URL signé vers une vidéo.
 */
class VideoUrlGenerator
{
    private string $baseUrl;
    private string $signatureKey;

    public function __construct(string $baseUrl, string $signatureKey)
    {
        $this->baseUrl = $baseUrl;
        $this->signatureKey = $signatureKey;
    }

    public function generate(string $videoPath, User $user): string
    {
        $premiumEnd = $user->getPremiumEnd();
        if (null === $premiumEnd) {
            throw new \RuntimeException('Impossible de générer une URL de vidéo pour un utilisateur non premium');
        }
        $videoPath = urlencode($videoPath);
        $signer = new Sha256();
        $token = (new Builder())
            ->issuedAt(time())
            ->expiresAt(time() + 3600 * 4)
            ->withClaim('premium', $premiumEnd->format('c'))
            ->getToken($signer, new Key($this->signatureKey));

        return "{$this->baseUrl}?path={$videoPath}&token={$token}";
    }
}
