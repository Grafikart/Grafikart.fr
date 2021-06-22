<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PodcastControllerTest extends WebTestCase
{
    use FixturesTrait;

    const CREATE_BUTTON = 'Proposer';
    const CREATE_FORM = '#podcast-form form';
    const TITLE_FIELD = 'podcast_form[title]';

    public function testCreatePodcastWithNewAccount(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $user->setCreatedAt(new \DateTime());
        $this->em->flush();
        $this->login($user);
        $this->client->request('GET', '/podcasts/votes');
        $this->assertResponseStatusCodeSame(200);
        $this->expectAlert('info', 'Vous devez avoir un compte actif');
    }

    public function testCreatePodcastWithOldAccount(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $user->setCreatedAt(new \DateTime('2000-02-02'));
        $this->em->flush();
        $this->login($user);
        $crawler = $this->client->request('GET', '/podcasts/votes');
        $form = $crawler->filter('#podcast-form form')->form();
        $form->setValues([
            'podcast_form[title]' => 'Ceci est un test de podcast',
        ]);
        $this->client->submit($form);
        $this->client->followRedirects();
        $this->assertResponseStatusCodeSame(200);
        $this->expectAlert('success');
    }

    public function testCreateEmptyPodcastIdea(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $user->setCreatedAt(new \DateTime('2000-02-02'));
        $this->em->flush();
        $this->login($user);
        $crawler = $this->client->request('GET', '/podcasts/votes');
        $form = $crawler->filter(self::CREATE_FORM)->form();
        $form->setValues([
           self::TITLE_FIELD => 'lol',
        ]);
        $this->client->submit($form);
        $this->expectFormErrors();
    }

    public function testCreateSubjectTwice(): void
    {
        $this->testCreatePodcastWithOldAccount();
        $crawler = $this->client->request('GET', '/podcasts/votes');
        $form = $crawler->filter(self::CREATE_FORM)->form();
        $form->setValues([
            self::TITLE_FIELD => 'Ceci est un test de podcast',
        ]);
        $this->client->submit($form);
        $this->expectFormErrors();
    }
}
