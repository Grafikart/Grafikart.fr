<?php

namespace App\Domain\Revision\Event;

use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Revision\Revision;
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
            RevisionRefusedEvent::class => 'onRevisionRefused',
            RevisionAcceptedEvent::class => 'onRevisionAccepted',
        ];
    }

    public function onRevisionRefused(RevisionRefusedEvent $event): void
    {
        $event->getRevision()->setStatus(Revision::REJECTED);
        $event->getRevision()->setContent('');
        $this->em->flush();
    }

    public function onRevisionAccepted(RevisionAcceptedEvent $event): void
    {
        $content = $event->getRevision()->getTarget();
        $previous = clone $content;
        $content->setContent($event->getRevision()->getContent());
        $event->getRevision()->setStatus(Revision::ACCEPTED);
        $event->getRevision()->setContent('');
        $this->em->flush();
        $this->dispatcher->dispatch(new ContentUpdatedEvent($content, $previous));
    }
}
