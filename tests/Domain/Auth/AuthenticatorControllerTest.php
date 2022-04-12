<?php

namespace App\Tests\Domain\Auth;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class AuthenticatorControllerTest extends WebTestCase
{

    use FixturesTrait;

    private const LOGIN_BUTTON = "Se connecter";
    private const LOGIN_PATH = '/connexion';

    public function testSuccessLogin (): void {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
        $form->setValues([
            'email' => $user->getUsername(),
            'password' => '0000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testFailLogin (): void {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
        $form->setValues([
            'email' => $user->getUsername(),
            'password' => '1000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects(self::LOGIN_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testBruteForce (): void {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        for($i = 0; $i < 3; $i++) {
            $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
            $form->setValues([
                'email'    => $user->getUsername(),
                'password' => '1000'
            ]);
            $this->client->submit($form);
            $this->assertResponseRedirects(self::LOGIN_PATH);
            $this->client->followRedirect();
        }
        $form = $crawler->selectButton(self::LOGIN_BUTTON)->form();
        $form->setValues([
            'email'    => $user->getUsername(),
            'password' => '0000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects(self::LOGIN_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

}
