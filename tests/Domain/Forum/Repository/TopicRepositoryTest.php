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

    public function testUsersToNotify ()
    {
        /** @var Message $message */
        ['message1' => $message, 'user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['forums']);
        $message->setAuthor($user1);
        $message->getTopic()->getAuthor($user2);
        $this->em->flush();
        $this->assertCount(
            1,
            $this->repository->findUsersToNotify($message)
        );
    }

    public function testUsersToNotifyExcludeUnwantedEmails ()
    {
        /** @var Message $message */
        /** @var User $user1 */
        ['message1' => $message, 'user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['forums']);
        $message->setAuthor($user1);
        $user2->setForumMailNotification(false);
        $message->getTopic()->getAuthor($user2);
        $this->em->flush();
        $this->assertCount(
            0,
            $this->repository->findUsersToNotify($message)
        );
    }
}
