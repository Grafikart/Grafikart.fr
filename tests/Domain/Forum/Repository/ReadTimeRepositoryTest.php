<?php

namespace App\Tests\Domain\Forum\Repository;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\ReadTime;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Repository\ReadTimeRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @property ReadTimeRepository $repository
 */
class ReadTimeRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;

    private Topic $topic;
    private User $user;
    private User $user2;
    protected $repositoryClass = ReadTimeRepository::class;

    public function setUp(): void
    {
        parent::setUp();
        $data = $this->loadFixtures(['forums']);
        $this->topic = $data['topic1'];
        $this->user = $data['user1'];
        $this->user2 = $data['user2'];
    }

    public function testReadTopicCreateANewReadTime(): void
    {
        $this->assertEquals(0, $this->repository->count([]));
        $this->repository->updateReadTimeForTopic($this->topic, $this->user);
        $this->em->flush();
        $this->assertEquals(1, $this->repository->count([]));
        $this->repository->updateReadTimeForTopic($this->topic, $this->user2);
        $this->em->flush();
        $this->assertEquals(2, $this->repository->count([]));
    }

    public function testReadTimeIsUpdated(): void
    {
        $this->topic->setUpdatedAt(new \DateTime('-1 day'));
        $oldReadDate = new \DateTime('-5 day');
        $lastReadTime = (new ReadTime())
            ->setReadAt($oldReadDate)
            ->setOwner($this->user)
            ->setTopic($this->topic);
        $this->em->persist($lastReadTime);
        $this->em->flush();
        $newLastReadtime = $this->repository->updateReadTimeForTopic($this->topic, $this->user, true);
        $this->assertGreaterThan($oldReadDate->getTimestamp(), $newLastReadtime->getReadAt()->getTimestamp());
    }

    public function testDoesNotTouchReadTimeIfTopicNotUpdated(): void
    {
        $this->topic->setUpdatedAt(new \DateTime('-5 day'));
        $oldReadDate = new \DateTime('-1 day');
        $lastReadTime = (new ReadTime())
            ->setReadAt($oldReadDate)
            ->setOwner($this->user)
            ->setTopic($this->topic);
        $this->em->persist($lastReadTime);
        $this->em->flush();
        $newLastReadtime = $this->repository->updateReadTimeForTopic($this->topic, $this->user);
        $this->assertEquals($oldReadDate->getTimestamp(), $newLastReadtime->getReadAt()->getTimestamp());
    }
}
