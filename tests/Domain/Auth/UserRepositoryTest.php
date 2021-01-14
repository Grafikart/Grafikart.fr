<?php

namespace App\Tests\Domain\Auth;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Tests\FixturesTrait;
use App\Tests\RepositoryTestCase;

/**
 * @property UserRepository $repository
 */
class UserRepositoryTest extends RepositoryTestCase
{
    use FixturesTrait;

    protected $repositoryClass = UserRepository::class;

    public function testCleanUsers()
    {
        /** @var User $user */
        /** @var User $user2 */
        ['user1' => $user, 'user2' => $user2] = $this->loadFixtures(['users']);
        $totalUser = $this->repository->count([]);
        $user->setDeleteAt(new \DateTimeImmutable('-10 days'));
        $user2->setDeleteAt(new \DateTimeImmutable('-10 days'));
        $this->em->flush();
        $deletions = $this->repository->cleanUsers();
        $this->assertSame(2, $deletions);
        $this->assertSame(
            $totalUser - 2,
            $this->repository->count([])
        );
    }

    public function testCleanUsersWithTheRightDate()
    {
        /** @var User $user */
        /** @var User $user2 */
        ['user1' => $user, 'user2' => $user2] = $this->loadFixtures(['users']);
        $totalUser = $this->repository->count([]);
        $user->setDeleteAt(new \DateTimeImmutable('-10 days'));
        $user2->setDeleteAt(new \DateTimeImmutable('+ 1 days'));
        $this->em->flush();
        $deletions = $this->repository->cleanUsers();
        $this->assertSame(1, $deletions);
        $this->assertSame(
            $totalUser - 1,
            $this->repository->count([])
        );
    }
}
