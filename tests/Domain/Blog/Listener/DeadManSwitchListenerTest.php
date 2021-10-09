<?php

namespace App\Tests\Domain\Blog\Listener;

use App\Domain\Blog\Listener\DeadManSwitchListener;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\PostRepository;
use App\Domain\History\Repository\ProgressRepository;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DeadManSwitchListenerTest extends EventSubscriberTest
{
    /**
     * @var MockObject|ProgressRepository
     */
    private MockObject $repository;

    /**
     * @var MockObject|EntityManagerInterface
     */
    private MockObject $em;

    /**
     * @var MockObject|AuthorizationCheckerInterface
     */
    private MockObject $auth;

    private DeadManSwitchListener $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(PostRepository::class);
        $this->auth = $this->createMock(AuthorizationCheckerInterface::class);
        $this->listener = new DeadManSwitchListener($this->auth, $this->repository, $this->em);
    }

    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(DeadManSwitchListener::class, AuthenticationEvents::AUTHENTICATION_SUCCESS);
    }

    public function testDoNothingIfNoPermission()
    {
        $post = new Post();
        $date = new \DateTime();
        $post->setCreatedAt($date);
        $this->auth->expects($this->any())->method('isGranted')->willReturn(false);
        $this->listener->onAdminDeath();
        $this->em->expects($this->never())->method('flush');
        $this->assertEquals($post->getCreatedAt(), $date);
    }

    public function testUpdateIfNoPost()
    {
        $this->repository->expects($this->any())->method('findOneBy')->willReturn(null);
        $this->auth->expects($this->any())->method('isGranted')->willReturn(true);
        $this->em->expects($this->never())->method('flush');
        $this->listener->onAdminDeath();
    }

    public function testUpdateDateTimeIfPermissionOk()
    {
        $post = new Post();
        $date = new \DateTime();
        $post->setCreatedAt($date);
        $this->repository->expects($this->any())->method('findOneBy')->willReturn($post);
        $this->auth->expects($this->any())->method('isGranted')->willReturn(true);
        $this->em->expects($this->once())->method('flush');
        $this->listener->onAdminDeath();
        $this->assertTrue($post->getCreatedAt() > new \DateTime('+6 days'));
    }
}
