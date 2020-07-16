<?php

namespace App\Tests\Domain\Forum\Repository;

use App\Domain\Forum\Repository\TopicRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @property TopicRepository $repository
 */
class TopicRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = TopicRepository::class;

    use FixturesTrait;

    public function testDeleteUsersTopic()
    {
        $data = $this->loadFixtures(['forums']);
        $topic1 = $data['topic1'];
        $topic2 = $data['topic2'];
        $user = $data['user1'];
        $topic1->setAuthor($user);
        $topic2->setAuthor($user);
        $this->em->flush();
        $this->repository->deleteForUser($user);
        $this->assertEquals(0, $this->repository->count([]));
    }
}
