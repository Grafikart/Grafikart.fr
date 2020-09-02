<?php

namespace App\Infrastructure\Social\Exception;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Erreur renvoyée lorsque l'on ne trouve pas d'utilisateur correspondant à la réponse de l'OAUTH.
 */
class UserOauthNotFoundException extends AuthenticationException
{
    private ResourceOwnerInterface $resourceOwner;

    public function __construct(ResourceOwnerInterface $resourceOwner)
    {
        $this->resourceOwner = $resourceOwner;
    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
        return $this->resourceOwner;
    }
}
