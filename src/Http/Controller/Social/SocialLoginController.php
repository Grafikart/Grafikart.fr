<?php

namespace App\Http\Controller\Social;

use App\Http\Controller\AbstractController;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SocialLoginController extends AbstractController
{
    private const SCOPES = [
        'github' => ['user:email'],
        'google' => [],
        'facebook' => ['email'],
    ];
    private ClientRegistry $clientRegistry;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    /**
     * @Route("/oauth/connect/{service}", name="oauth_connect")
     */
    public function connect(string $service): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES))) {
            throw new AccessDeniedException();
        }

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    /**
     * This is handled by the GithubAuthenticator.
     *
     * @Route("/oauth/check/{service}", name="oauth_check")
     */
    public function check(): Response
    {
        return new Response();
    }
}
