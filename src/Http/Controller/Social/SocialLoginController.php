<?php

namespace App\Http\Controller\Social;

use App\Http\Controller\AbstractController;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SocialLoginController extends AbstractController
{
    private const SCOPES = [
        'github' => ['user:email'],
    ];

    /**
     * @Route("/oauth/connect/{service}", name="oauth_connect")
     */
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES))) {
            throw new AccessDeniedHttpException();
        }

        return $clientRegistry->getClient($service)->redirect(self::SCOPES[$service], []);
    }

    /**
     * This is handled by the GithubAuthenticator.
     *
     * @Route("/oauth/check/{service}", name="oauth_check")
     */
    public function check(): void
    {
        return;
    }
}
