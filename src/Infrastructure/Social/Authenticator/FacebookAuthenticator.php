<?php

namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Infrastructure\Social\Exception\EmailAlreadyUsedException;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FacebookAuthenticator extends AbstractSocialAuthenticator
{
    use TargetPathTrait;

    protected string $serviceName = 'facebook';

    public function getUserFromResourceOwner(ResourceOwnerInterface $facebookUser, UserRepository $repository): ?User
    {
        if (!($facebookUser instanceof FacebookUser)) {
            throw new \RuntimeException('Expecting FacebookClient as the first parameter');
        }
        $user = $repository->findForOauth('facebook', $facebookUser->getId(), $facebookUser->getEmail());
        if ($user && null === $user->getFacebookId()) {
            throw new EmailAlreadyUsedException();
        }

        return $user;
    }
}
