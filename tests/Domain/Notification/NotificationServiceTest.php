<?php

namespace App\Tests\Domain\Notification;

use App\Domain\Auth\User;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\Event\NotificationReadEvent;
use App\Domain\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationServiceTest extends TestCase
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private EventDispatcherInterface $dispatcher;
    private NotificationService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->serializer->expects($this->any())->method('serialize')->willReturn('/chemin');
        $security = $this->getMockBuilder(Security::class)->disableOriginalConstructor()->getMock();
        $security->expects($this->any())->method('isGranted')->willReturn(true);
        $this->service = new NotificationService($this->serializer, $this->em, $this->dispatcher, $security);
    }

    public function testDispatchEvent(): void
    {
        $entity = new FakeEntity(10);
        $this->dispatcher->expects($this->once())->method('dispatch')->with(
            $this->isInstanceOf(NotificationCreatedEvent::class)
        );
        $this->service->notifyChannel('demo', 'Bonjour', $entity);
    }

    public function testSentReadEvent(): void
    {
        $user = (new User());
        $previousDate = new \DateTimeImmutable('- 10 days');
        $user->setNotificationsReadAt($previousDate);
        $this->dispatcher->expects($this->once())->method('dispatch')->with($this->isInstanceOf(NotificationReadEvent::class));
        $this->service->readAll($user);
        $this->assertNotEquals($previousDate, $user->getNotificationsReadAt());
    }
}
