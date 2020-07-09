<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class EventSubscriberTest extends KernelTestCase
{
    protected function dispatch(EventSubscriberInterface $subscriber, object $event): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }

    /**
     * Vérifie qu'un subscriber écoute bien un évènement donnée au niveau du kernel.
     */
    protected function assertSubsribeTo(string $subscriberClass, string $event): void
    {
        self::bootKernel();
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::$container->get(EventDispatcherInterface::class);
        $subscribers = $dispatcher->getListeners($event);
        // TODO : Ne fonctionne pour le moment qu'avec des subscribers, à voir les listeners
        $subscribers = array_map(fn ($subscriber) => get_class($subscriber[0]), $subscribers);
        $this->assertContains($subscriberClass, $subscribers);
    }
}
