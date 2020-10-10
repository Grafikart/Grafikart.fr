<?php

declare(strict_types=1);

namespace App\Tests\Http\Api\Controller;

use App\Tests\ApiTestCase;
use App\Tests\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class CountryControllerTest extends ApiTestCase
{
    use FixturesTrait;

    public function testGetWithoutAuth()
    {
        $this->client->request('GET', '/api/country');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testWithAuth()
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $this->login($user);
        $this->client->request('GET', '/api/country');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'FR' => 'France',
        ]);
    }
}
