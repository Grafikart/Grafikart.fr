<?php

namespace App\Tests\Controller;

use App\Domain\Auth\User;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class PasswordControllerTest extends WebTestCase
{

    const RESET_PASSWORD_PATH = '/password/new';
    const RESET_PASSWORD_BUTTON = 'M\'envoyer les instructions';

    use FixturesTrait;

    public function testResetPasswordIsReachableFromLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $this->assertEquals('Mot de passe oublié', $crawler->filter('h1')->text());
    }

    public function testResetPasswordBlockBadEmails(): void
    {
        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => 'lol hacker',
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
    }

    public function testResetPasswordShouldSendAnEmail(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);

        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        $this->assertEmailCount(1);
    }

    public function testResetPasswordShouldBlockRepeat(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);

        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);

        // Je demande un nouveau mot de passe
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);

        // Je demande encore un nouveau mot de passe
        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->expectErrorAlert();
    }

    public function testResetPasswordShouldWorkWithOldPasswordAttempt(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['password-reset']);
        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->assertEmailCount(1);
    }

    public function testResetPasswordConfirmChangePassword(): void
    {
        /** @var array<string,PasswordResetToken> $tokens */
        $tokens = $this->loadFixtures(['password-reset']);
        /** @var PasswordResetToken $token */
        $token = $tokens['recent_password_token'];
        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH . "/{$token->getUser()->getId()}/{$token->getToken()}");
        $this->client->submitForm('Réinitialiser mon mot de passe', [
            'password' => [
                'first' => 'pazjejoazuaziuaazenonazbfiumqksdmù',
                'second' => 'pazjejoazuaziuaazenonazbfiumqksdmù'
            ],
        ]);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testResetPasswordConfirmExpired(): void
    {
        /** @var array<string,PasswordResetToken> $tokens */
        $tokens = $this->loadFixtures(['password-reset']);
        /** @var PasswordResetToken $token */
        $token = $tokens['password_token'];
        $this->client->request('GET', self::RESET_PASSWORD_PATH . "/{$token->getUser()->getId()}/{$token->getToken()}");
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }
}
