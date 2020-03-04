<?php

namespace App\Domain\Revision\Event;

use App\Domain\Application\Event\ContentUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RevisionSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RevisionRefusedEvent::class  => 'onRevisionRefused',
            RevisionAcceptedEvent::class => 'onRevisionAccepted',
        ];
    }

    public function onRevisionRefused(RevisionRefusedEvent $event): void
    {
        $this->em->remove($event->getRevision());
        $this->em->flush();
    }

    public function onRevisionAccepted(RevisionAcceptedEvent $event): void
    {
        $event->getRevision()->getTarget()->setContent($event->getRevision()->getContent());
        $this->em->remove($event->getRevision());
        $this->em->flush();
        $this->dispatcher->dispatch(new ContentUpdatedEvent($event->getRevision()->getTarget()));
    }
}
