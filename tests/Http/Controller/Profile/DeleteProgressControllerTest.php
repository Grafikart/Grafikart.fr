<?php

namespace App\Tests\Http\Controller\Profile;

use App\Domain\History\Entity\Progress;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeleteProgressControllerTest extends WebTestCase
{

    use FixturesTrait;

    public function testDeleteSuccess()
    {
        /** @var Progress $progress */
        ['progress' => $progress] = $this->loadFixtures(['progress']);
        $this->login($progress->getAuthor());
        $this->jsonRequest('DELETE', '/progress/' . $progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDeleteFailUnauthenticated()
    {
        /** @var Progress $progress */
        ['progress' => $progress] = $this->loadFixtures(['progress']);
        $this->jsonRequest('DELETE', '/progress/' . $progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteFailOnBadUser()
    {
        /** @var Progress $progress */
        /** @var Progress $user */
        ['progress' => $progress, 'user2' => $user] = $this->loadFixtures(['progress']);
        $this->login($user);
        $this->jsonRequest('DELETE', '/progress/' . $progress->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
