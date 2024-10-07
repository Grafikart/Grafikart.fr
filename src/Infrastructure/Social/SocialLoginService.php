<?php

namespace App\Infrastructure\Social;

use App\Domain\Auth\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SocialLoginService
{
    final public const SESSION_KEY = 'oauth_login';

    public function __construct(
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function persist(SessionInterface $session, ResourceOwnerInterface $resourceOwner): void
    {
        $data = $this->normalizer->normalize($resourceOwner);
        $session->set(self::SESSION_KEY, $data);
        $session->save();
    }

    public function hydrate(SessionInterface $session, User $user): bool
    {
        $oauthData = $session->get(self::SESSION_KEY);
        if (null === $oauthData || !isset($oauthData['email'])) {
            return false;
        }
        $user->setEmail($oauthData['email']);
        $user->setGithubId($oauthData['github_id'] ?? null);
        $user->setGoogleId($oauthData['google_id'] ?? null);
        $user->setFacebookId($oauthData['facebook_id'] ?? null);
        $user->setUsername($oauthData['username']);
        $user->setConfirmationToken(null);

        return true;
    }

    public function getOauthType(SessionInterface $session): ?string
    {
        $oauthData = $session->get(self::SESSION_KEY);

        return $oauthData ? $oauthData['type'] : null;
    }
}
