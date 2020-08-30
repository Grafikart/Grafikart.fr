<?php

namespace App\Infrastructure\Social;

use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SocialLoginService
{
    private const SESSION_KEY = 'oauth_login';

    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function persist(ResourceOwnerInterface $resourceOwner): void
    {
        $data = [];
        if ($resourceOwner instanceof GithubResourceOwner) {
            $data = [
                'email' => $resourceOwner->getEmail(),
                'github_id' => $resourceOwner->getId(),
                'type' => 'Github',
                'username' => $resourceOwner->getNickname(),
            ];
        }
        $this->session->set(self::SESSION_KEY, $data);
    }

    public function hydrate(\App\Domain\Auth\User $user): void
    {
        $oauthData = $this->session->get(self::SESSION_KEY);
        if (null === $oauthData) {
            return;
        }
        $user->setEmail($oauthData['email'] ?? '');
        $user->setGithubId($oauthData['github_id'] ?? null);
        $user->setUsername($oauthData['username'] ?? '');
        $user->setConfirmationToken(null);
    }

    public function getOauthType(): ?string
    {
        $oauthData = $this->session->get(self::SESSION_KEY);

        return $oauthData ? $oauthData['type'] : null;
    }
}
