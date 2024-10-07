<?php

namespace App\Tests\Domain\Blog\Listener;

use App\Domain\Auth\User;
use App\Domain\Blog\Listener\DeadManSwitchListener;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\PostRepository;
use App\Domain\History\Repository\ProgressRepository;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

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
     * @var MockObject|AccessDecisionManagerInterface
     */
    private MockObject $permission;

    private DeadManSwitchListener $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(PostRepository::class);
        $this->permission = $this->createMock(AccessDecisionManagerInterface::class);
        $this->listener = new DeadManSwitchListener($this->permission, $this->repository, $this->em);
    }

    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(DeadManSwitchListener::class, AuthenticationEvents::AUTHENTICATION_SUCCESS);
    }

    private function getEvent(): AuthenticationSuccessEvent
    {
        $token = $this->createMock(TokenInterface::class);
        $user = new User();
        $token->expects($this->any())->method('getUser')->willReturn($user);

        return new AuthenticationSuccessEvent($token);
    }

    public function testDoNothingIfNoPermission()
    {
        $post = new Post();
        $date = new \DateTimeImmutable();
        $post->setCreatedAt($date);
        $this->permission->expects($this->any())->method('decide')->willReturn(false);
        $this->listener->onAdminDeath($this->getEvent());
        $this->em->expects($this->never())->method('flush');
        $this->assertEquals($post->getCreatedAt(), $date);
    }

    public function testUpdateIfNoPost()
    {
        $this->repository->expects($this->any())->method('findOneBy')->willReturn(null);
        $this->permission->expects($this->any())->method('decide')->willReturn(true);
        $this->em->expects($this->never())->method('flush');
        $this->listener->onAdminDeath($this->getEvent());
    }

    public function testUpdateDateTimeIfPermissionOk()
    {
        $post = new Post();
        $date = new \DateTimeImmutable();
        $post->setCreatedAt($date);
        $this->repository->expects($this->any())->method('findOneBy')->willReturn($post);
        $this->permission->expects($this->any())->method('decide')->willReturn(true);
        $this->em->expects($this->once())->method('flush');
        $this->listener->onAdminDeath($this->getEvent());
        $this->assertTrue($post->getCreatedAt() > new \DateTimeImmutable('+6 days'));
    }
}
