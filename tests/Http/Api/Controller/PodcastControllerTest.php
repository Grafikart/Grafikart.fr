<?php

declare(strict_types=1);

namespace App\Tests\Http\Api\Controller;

use App\Domain\Auth\User;
use App\Domain\Podcast\Entity\Podcast;
use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;

class PodcastControllerTest extends ApiTestCase
{
    use FixturesTrait;

    public function testVoteOnPodcast(): void
    {
        /** @var Podcast $podcast */
        /** @var User $user */
        [
            'podcast_suggestions1' => $podcast,
            'user1' => $user
        ] = $this->loadFixtures(['users', 'podcasts']);
        $this->login($user);
        $count = $podcast->getVotesCount();

        // On vote une fois
        $this->client->request('POST', "/api/podcasts/{$podcast->getId()}/vote");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['votesCount' => $count + 1]);

        // On retire notre vote
        $this->client->request('POST', "/api/podcasts/{$podcast->getId()}/vote");
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['votesCount' => $count]);
    }

    public function testCantVoteOnSuggestedPodcast(): void
    {
        /** @var Podcast $podcast */
        /** @var User $user */
        [
            'podcast_suggestions1' => $podcast,
            'user2' => $user
        ] = $this->loadFixtures(['users', 'podcasts']);
        $this->login($user);

        $this->client->request('POST', "/api/podcasts/{$podcast->getId()}/vote");
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCantVoteOnConfirmedPodcast(): void
    {
        /** @var Podcast $podcast */
        /** @var User $user */
        [
            'podcast1' => $podcast,
            'user1' => $user
        ] = $this->loadFixtures(['users', 'podcasts']);
        $this->login($user);

        $this->client->request('POST', "/api/podcasts/{$podcast->getId()}/vote");
        $this->assertResponseStatusCodeSame(403);
    }
}
