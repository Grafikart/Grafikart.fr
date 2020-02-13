<?php

namespace App\Tests\Domain\Auth\Service;

use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\LoginAttemptService;
use App\Domain\Auth\Repository\LoginAttemptRepository;
use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginAttemptServiceTest extends TestCase
{

    public function testAddAttempt(): void
    {
        /** @var MockObject|EntityManagerInterface $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        /** @var MockObject|LoginAttemptRepository $repository */
        $repository = $this->getMockBuilder(LoginAttemptRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $service = new LoginAttemptService($repository, $em);
        $user = new User();

        $em->expects($this->once())->method('persist')->with(
            $this->callback(function (LoginAttempt $attempt) use ($user) {
                return $attempt->getUser() === $user;
            })
        );
        $em->expects($this->once())->method('flush');

        $service->addAttempt($user);
    }

}
