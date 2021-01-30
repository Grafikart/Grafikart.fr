<?php

namespace App\Tests\Domain\Forum\Repository;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Repository\TopicRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @property TopicRepository $repository
 */
class TopicRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;
    protected $repositoryClass = TopicRepository::class;

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

    public function testUsersToNotify()
    {
        /** @var Message $message */
        ['message1' => $message, 'message2' => $message2] = $this->loadFixtures(['forums']);
        $this->em->flush();
        $this->assertCount(
            1,
            $this->repository->findUsersToNotify($message)
        );
        $this->assertCount(
            1,
            $this->repository->findUsersToNotify($message2)
        );
    }

    public function testUsersToNotifyExcludeUnwantedEmails()
    {
        /** @var Message $message */
        /** @var User $user1 */
        ['message2' => $message, 'user1' => $user] = $this->loadFixtures(['forums']);
        $user->setForumMailNotification(false);
        $this->em->flush();
        $this->assertCount(
            0,
            $this->repository->findUsersToNotify($message)
        );
    }
}
