<?php

namespace App\Infrastructure\Social;

use App\Domain\Auth\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SocialLoginService
{
    public const SESSION_KEY = 'oauth_login';

    private SessionInterface $session;

    private NormalizerInterface $normalizer;

    public function __construct(SessionInterface $session, NormalizerInterface $normalizer)
    {
        $this->session = $session;
        $this->normalizer = $normalizer;
    }

    public function persist(ResourceOwnerInterface $resourceOwner): void
    {
        $data = $this->normalizer->normalize($resourceOwner);
        $this->session->set(self::SESSION_KEY, $data);
    }

    public function hydrate(User $user): bool
    {
        $oauthData = $this->session->get(self::SESSION_KEY);
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

    public function getOauthType(): ?string
    {
        $oauthData = $this->session->get(self::SESSION_KEY);

        return $oauthData ? $oauthData['type'] : null;
    }
}
