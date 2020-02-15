<?php

namespace App\Tests\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testLive(): void
    {
        $title = "Se connecter";

        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals($title, $crawler->filter('h1')->text());
    }

    public function testBadPassword(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => 'john@doe.fr',
            'password' => '00000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testGoodPasswordWorks(): void
    {

        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/login');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testAttemptLimit(): void
    {

        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/login');
        $this->expectFormErrors(0);
        for ($i = 0; $i < 4; $i++) {
            $form = $crawler->selectButton('Se connecter')->form();
            $form->setValues([
                'email' => $users['user1']->getEmail(),
                'password' => '00000'
            ]);
            $this->client->submit($form);
            $this->assertResponseRedirects();
            $crawler = $this->client->followRedirect();
        }
        $this->assertStringContainsString('verrouillé', $crawler->filter('alert-message')->text());
    }

    public function testResetPassword(): void
    {

        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $this->assertEquals('Mot de passe oublié', $crawler->filter('h1')->text());
    }

    public function testResetPasswordBlockBadEmails(): void
    {

        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('M\'envoyer les instructions')->form();
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

        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('M\'envoyer les instructions')->form();
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

        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $url = $crawler->getUri();

        // Je demande un nouveau mot de passe
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('M\'envoyer les instructions')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);

        // Je demande encore un nouveau mot de passe
        $crawler = $this->client->request('GET', $url);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('M\'envoyer les instructions')->form();
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
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Mot de passe oublié ?')->link());
        $form = $crawler->selectButton('M\'envoyer les instructions')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->assertEmailCount(1);
    }

    public function testResetPasswordAfterSuccess(): void
    {
        // TODO : Tester que l'on peut relancer une demande de réinitialisation après une demande complété
    }

    public function testResetPasswordConfirmChangePassword(): void
    {
        // TODO : Vérifier que le mot de passe de l'utilisateur est bien changé
    }

    public function testResetPasswordConfirmExpired(): void
    {
        // TODO : Vérifier que l'on soit bien redirigé si le token est invalid
    }
}
