<?php

namespace App\Http\Controller\Social;

use App\Domain\Auth\User;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\DiscordClient;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordController extends AbstractController
{
    #[Route(path: '/discord/connect', name: 'oauth_discord')]
    #[IsGranted('ROLE_USER')]
    public function connect(DiscordClient $client): RedirectResponse
    {
        return $client->redirect(['identify', 'email']);
    }

    #[Route(path: '/oauth/check/discord', name: 'oauth_discord_check')]
    #[IsGranted('ROLE_USER')]
    public function check(DiscordClient $client, EntityManagerInterface $em): RedirectResponse
    {
        try {
            /** @var DiscordResourceOwner $discordUser */
            $discordUser = $client->fetchUser();
            /** @var User $user */
            $user = $this->getUser();

            $user->setDiscordId($discordUser->getId());
            $em->flush();
            $this->addFlash('success', 'Votre compte discord a bien été lié');
        } catch (MissingAuthorizationCodeException) {
            // Do nothing
        }

        return $this->redirectToRoute('user_edit');
    }
}
