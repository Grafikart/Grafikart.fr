<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testLoginTitle(): void
    {
        $title = "Se connecter";
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals($title, $crawler->filter('h1')->text(), $crawler->filter('title')->text());
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
}
