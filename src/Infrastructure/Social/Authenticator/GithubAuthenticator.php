<?php

namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Infrastructure\Social\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class GithubAuthenticator.
 */
class GithubAuthenticator extends AbstractSocialAuthenticator
{
    use TargetPathTrait;

    private HttpClientInterface $http;

    protected string $serviceName = 'github';

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        SocialLoginService $socialLoginService,
        HttpClientInterface $httpClient
    ) {
        parent::__construct($clientRegistry, $em, $router, $socialLoginService);
        $this->http = $httpClient;
    }

    public function getUserFromResourceOwner(ResourceOwnerInterface $githubUser, UserRepository $repository): ?User
    {
        if (!($githubUser instanceof GithubResourceOwner)) {
            throw new \RuntimeException('Expecting GithubResourceOwner as the first parameter');
        }
        $user = $repository->findForOauth('github', $githubUser->getId(), $githubUser->getEmail());
        if ($user && null === $user->getGithubId()) {
            $user->setGithubId($githubUser->getId());
            $this->em->flush();
        }

        return $user;
    }

    public function getResourceOwnerFromCredentials(AccessToken $credentials): GithubResourceOwner
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = parent::getResourceOwnerFromCredentials($credentials);
        $response = $this->http->request(
            'GET',
            'https://api.github.com/user/emails',
            [
                'headers' => [
                    'authorization' => "token {$credentials->getToken()}",
                ],
            ]
        );
        $emails = json_decode($response->getContent(), true);
        foreach ($emails as $email) {
            if (true === $email['primary'] && true === $email['verified']) {
                $data = $githubUser->toArray();
                $data['email'] = $email['email'];

                return new GithubResourceOwner($data);
            }
        }

        return $githubUser;
    }
}
