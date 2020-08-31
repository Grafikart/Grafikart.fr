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
    ];

    /**
     * @Route("/oauth/connect/{service}", name="oauth_connect")
     */
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES))) {
            throw new AccessDeniedException();
        }

        return $clientRegistry->getClient($service)->redirect(self::SCOPES[$service], []);
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
