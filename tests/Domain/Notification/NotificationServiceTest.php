<?php

namespace App\Tests\Domain\Notification;

use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationServiceTest extends TestCase
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private EventDispatcherInterface $dispatcher;
    private NotificationService $service;

    public function setUp(): void
    {
        $this->serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $this->em = $this->getMockBuilder(EntityManagerInterface::class)->getMock();
        $this->dispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->serializer->expects($this->any())->method('serialize')->willReturn('/chemin');
        $this->service = new NotificationService($this->serializer, $this->em, $this->dispatcher);
    }

    public function testDispatchEvent(): void
    {
        $entity = new FakeEntity(10);
        $this->dispatcher->expects($this->once())->method('dispatch')->with(
            $this->isInstanceOf(NotificationCreatedEvent::class)
        );
        $this->service->notifyChannel('demo', 'Bonjour', $entity);
    }

    public function testDoNotDispatchEventTwice(): void
    {
        $entity = new FakeEntity(10);
        $this->dispatcher->expects($this->once())->method('dispatch')->with(
            $this->isInstanceOf(NotificationCreatedEvent::class)
        );
        $this->service->notifyChannel('demo', 'Bonjour', $entity);
    }
}
