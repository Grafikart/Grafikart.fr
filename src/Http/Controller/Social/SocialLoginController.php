<?php

namespace App\Http\Controller\Social;

use App\Domain\Auth\AuthService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    /**
     * @Route("/oauth/unlink/{service}", name="oauth_unlink")
     * @IsGranted("ROLE_USER")
     */
    public function disconnect(string $service, AuthService $authService, EntityManagerInterface $em): RedirectResponse
    {
        $this->ensureServiceAccepted($service);
        $method = 'set'.ucfirst($service).'Id';
        $authService->getUser()->$method(null);
        $em->flush();
        $this->addFlash('success', 'Votre compte a bien été dissocié de '.$service);

        return $this->redirectToRoute('user_edit');
    }

    /**
     * @Route("/oauth/check/{service}", name="oauth_check")
     */
    public function check(): Response
    {
        return new Response();
    }

    private function ensureServiceAccepted(string $service): void
    {
        if (!in_array($service, array_keys(self::SCOPES))) {
            throw new AccessDeniedException();
        }
    }
}
