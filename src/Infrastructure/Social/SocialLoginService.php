<?php

namespace App\Infrastructure\Social;

use App\Domain\Auth\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SocialLoginService
{
    public const SESSION_KEY = 'oauth_login';

    private NormalizerInterface $normalizer;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
        $this->requestStack = $requestStack;
    }

    public function persist(ResourceOwnerInterface $resourceOwner): void
    {
        $data = $this->normalizer->normalize($resourceOwner);
        $this->requestStack->getSession()->set(self::SESSION_KEY, $data);
    }

    public function hydrate(User $user): bool
    {
        $oauthData = $this->requestStack->getSession()->get(self::SESSION_KEY);
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
        $oauthData = $this->requestStack->getSession()->get(self::SESSION_KEY);

        return $oauthData ? $oauthData['type'] : null;
    }
}
