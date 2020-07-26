<?php

namespace App\Tests\Domain\Forum\Repository;

use App\Domain\Forum\Entity\ReadTime;
use App\Domain\Forum\Repository\ReadTimeRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @property ReadTimeRepository $repository
 */
class ReadTimeRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;

    protected $repositoryClass = ReadTimeRepository::class;

    public function testReadTopicCreateANewReadTime(): void
    {
        $data = $this->loadFixtures(['forums']);
        $topic = $data['topic1'];
        $user = $data['user1'];
        $user2 = $data['user2'];
        $this->assertEquals(0, $this->repository->count([]));
        $this->repository->updateReadTimeForTopic($topic, $user);
        $this->em->flush();
        $this->assertEquals(1, $this->repository->count([]));
        $this->repository->updateReadTimeForTopic($topic, $user2);
        $this->em->flush();
        $this->assertEquals(2, $this->repository->count([]));
    }

    public function testReadTimeIsUpdated(): void
    {
        $data = $this->loadFixtures(['forums']);
        $topic = $data['topic1'];
        $user = $data['user1'];
        $user2 = $data['user2'];
        $topic->setUpdatedAt(new \DateTime('-1 day'));
        $oldReadDate = new \DateTime('-5 day');
        $lastReadTime = (new ReadTime())
            ->setReadAt($oldReadDate)
            ->setOwner($user)
            ->setTopic($topic);
        $this->em->persist($lastReadTime);
        $this->em->flush();
        $newLastReadtime = $this->repository->updateReadTimeForTopic($topic, $user, true);
        $this->assertGreaterThan($oldReadDate->getTimestamp(), $newLastReadtime->getReadAt()->getTimestamp());
    }

    public function testDoesNotTouchReadTimeIfTopicNotUpdated(): void
    {
        $data = $this->loadFixtures(['forums']);
        $topic = $data['topic1'];
        $user = $data['user1'];
        $user2 = $data['user2'];
        $topic->setUpdatedAt(new \DateTime('-5 day'));
        $oldReadDate = new \DateTime('-1 day');
        $lastReadTime = (new ReadTime())
            ->setReadAt($oldReadDate)
            ->setOwner($user)
            ->setTopic($topic);
        $this->em->persist($lastReadTime);
        $this->em->flush();
        $newLastReadtime = $this->repository->updateReadTimeForTopic($topic, $user);
        $this->assertEquals($oldReadDate->getTimestamp(), $newLastReadtime->getReadAt()->getTimestamp());
    }

    public function testUpdateNotificationStatusIfTopicAlreadyRead(): void
    {
        $data = $this->loadFixtures(['forum_topic']);
        $user1 = $data['user1'];
        $user2 = $data['user2'];
        $topic = $data['topic1'];
        $this->repository->updateReadTimeForTopic($topic, $user1);
        $this->em->flush();
        $this->repository->updateNotificationStatusForUsers($topic, [$user1]);
        $readTime = $this->repository->findOneBy(['owner' => $user1]);
        $this->assertTrue($readTime->isNotified());
    }

    public function testUpdateNotificationStatus(): void
    {
        $data = $this->loadFixtures(['forum_topic']);
        $user1 = $data['user1'];
        $user2 = $data['user2'];
        $topic = $data['topic1'];
        $this->repository->updateNotificationStatusForUsers($topic, [
            $user1,
            $user2,
        ]);
        $readTimes = $this->repository->findAll();
        $this->assertCount(2, $readTimes);
        $this->assertTrue($readTimes[0]->isNotified());
        $this->assertTrue($readTimes[1]->isNotified());
    }

    public function testUpdateResetNotifiedStatus(): void
    {
        $data = $this->loadFixtures(['forum_topic']);
        $user1 = $data['user1'];
        $user2 = $data['user2'];
        $topic = $data['topic1'];
        $this->repository->updateNotificationStatusForUsers($topic, [
            $user1,
            $user2,
        ]);
        $readTimes = $this->repository->findAll();
        $this->assertCount(2, $readTimes);
        $readTime = $readTimes[0];
        $this->repository->updateReadTimeForTopic(
            $readTime->getTopic(),
            $readTime->getOwner()
        );
        $this->assertFalse($readTimes[0]->isNotified());
    }
}
