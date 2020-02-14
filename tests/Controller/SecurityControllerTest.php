<?php

namespace App\Tests\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testLive(): void
    {
        $title = "Se connecter";
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals($title, $crawler->filter('h1')->text());
    }

    public function testBadPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(0, $crawler->filter('alert-message')->count());
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => 'john@doe.fr',
            'password' => '00000'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('alert-message')->count());
    }

    public function testGoodPasswordWorks(): void
    {
        $client = static::createClient();
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(0, $crawler->filter('alert-message')->count());
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testAttemptLimit(): void
    {
        $client = static::createClient();
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $client->request('GET', '/login');
        $this->assertEquals(0, $crawler->filter('alert-message')->count());
        for ($i = 0; $i < 4; $i++) {
            $form = $crawler->selectButton('Se connecter')->form();
            $form->setValues([
                'email' => $users['user1']->getEmail(),
                'password' => '00000'
            ]);
            $client->submit($form);
            $this->assertResponseRedirects();
            $crawler = $client->followRedirect();
        }
        $this->assertStringContainsString('verrouillé', $crawler->filter('alert-message')->text());
    }

}
