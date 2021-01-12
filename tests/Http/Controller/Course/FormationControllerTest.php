<?php

namespace App\Tests\Http\Controller\Course;

use App\Domain\Course\Entity\Formation;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormationControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testIndexSuccess()
    {
        $this->loadFixtures(['courses']);
        $this->client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle('Toutes les formations');
        $this->expectH1('Toutes les formations');
    }

    public function testSingleSuccess()
    {
        /** @var Formation $formation */
        ['formation1' => $formation] = $this->loadFixtures(['formations']);
        $this->client->request('GET', "/formations/{$formation->getSlug()}");
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->expectTitle("Formation {$formation->getTitle()}");
        $this->expectH1($formation->getTitle());
    }
}
