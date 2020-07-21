<?php

namespace App\Tests\Http\Controller;

use App\Domain\Course\Entity\Technology;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testBasicSearch(): void
    {
        $technologies = $this->loadFixtures(['technologies']);
        /** @var Technology $technology */
        $technology = $technologies['technology1'];
        $this->client->request('GET', '/recherche?q='.$technology->getName());
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/tutoriels/'.$technology->getSlug());
    }

    public function testBasicSearchWithLowercase(): void
    {
        $technologies = $this->loadFixtures(['technologies']);
        /** @var Technology $technology */
        $technology = $technologies['technology1'];
        $this->client->request('GET', '/recherche?q='.strtolower($technology->getName()));
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/tutoriels/'.$technology->getSlug());
    }
}
