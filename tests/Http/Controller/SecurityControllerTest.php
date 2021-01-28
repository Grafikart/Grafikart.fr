<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeServices;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testLoginTitle(): void
    {
        $title = 'Se connecter';
        $crawler = $this->client->request('GET', '/connexion');
        $this->assertEquals($title, $crawler->filter('h1')->text(), $crawler->filter('title')->text());
    }

    public function testBadPassword(): void
    {
        $crawler = $this->client->request('GET', '/connexion');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => 'john@doe.fr',
            'password' => '00000',
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
        $crawler = $this->client->request('GET', '/connexion');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testRedirectAfterLogin(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/connexion?redirect=/tutoriels');
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/tutoriels');
    }

    public function testBlockRedirectToOtherDomains(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/connexion?redirect=https://google.com');
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testAttemptLimit(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/connexion');
        $this->expectFormErrors(0);
        for ($i = 0; $i < 4; ++$i) {
            $form = $crawler->selectButton('Se connecter')->form();
            $form->setValues([
                'email' => $users['user1']->getEmail(),
                'password' => '00000',
            ]);
            $this->client->submit($form);
            $this->assertResponseRedirects();
            $crawler = $this->client->followRedirect();
        }
        $this->assertStringContainsString('verrouillé', $crawler->filter('alert-message')->text());
    }

    public function testPreventLoginUnconfirmedUser(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', '/connexion');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user_unconfirmed']->getEmail(),
            'password' => '00000',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testCookieAuthentication(): void
    {
        /** @var User $user */
        ['user1' => $user, 'course1' => $course] = $this->loadFixtures(['users', 'courses']);
        $crawler = $this->client->request('GET', '/connexion');
        $this->expectFormErrors(0);
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $user->getEmail(),
            'password' => '0000',
            '_remember_me' => "on",
        ]);
        $this->client->submit($form);
        $cookie = $this->client->getCookieJar()->get('REMEMBERME');
        $this->assertNotEmpty($cookie);
        $this->client->restart();
        $this->client->getCookieJar()->set(new Cookie('REMEMBERME', $cookie));
        $this->client->request('GET', "/tutoriels/{$course->getId()}/sources");
        $this->assertResponseRedirects('/premium');
    }
}
