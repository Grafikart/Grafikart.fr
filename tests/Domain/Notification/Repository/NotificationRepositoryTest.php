<?php

namespace App\Tests\Domain\Notification\Repository;

use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;

    /**
     * @var NotificationRepository
     */
    protected $repository;
    protected $repositoryClass = NotificationRepository::class;

    public function testFindForUser()
    {
        $fixtures = $this->loadFixtures(['users', 'notifications']);
        $notifications = $this->repository->findRecentForUser($fixtures['user1']);
        $notifications2 = $this->repository->findRecentForUser($fixtures['user2']);
        $this->assertCount(5, $notifications);
        $this->assertCount(0, $notifications2);
    }

    public function testPersistOrUpdateWithoutPreviousRecord()
    {
        $fixtures = $this->loadFixtures(['users', 'notifications']);
        /** @var Notification $newNotification */
        $newNotification = clone $fixtures['notification2'];
        $newNotification->setTarget('target::1');
        $this->repository->persistOrUpdate($newNotification);
        $this->em->flush();
        $this->assertCount(6, $this->repository->findRecentForUser($fixtures['user1']));
    }

    public function testPersistOrUpdateWithPreviousRecord()
    {
        $fixtures = $this->loadFixtures(['users', 'notifications']);
        /** @var Notification $newNotification */
        $newNotification = clone $fixtures['notification2'];
        $this->repository->persistOrUpdate($newNotification);
        $this->em->flush();
        $this->assertCount(5, $this->repository->findRecentForUser($fixtures['user1']));
    }
}
