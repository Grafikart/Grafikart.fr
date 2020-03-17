<?php

namespace App\Domain\History\Listener;

use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressionSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProgressEvent::class => 'onProgress'
        ];
    }

    public function onProgress(ProgressEvent $event): void
    {
        $progress = $this->em->getRepository(Progress::class)->findOneBy([
            'content' => $event->getContent(),
            'author'  => $event->getUser()
        ]);
        if ($progress === null) {
            $progress = (new Progress())
                ->setUpdatedAt(new \DateTime())
                ->setCreatedAt(new \DateTime())
                ->setAuthor($event->getUser())
                ->setContent($event->getContent())
                ->setPercent($event->getPercent());
            $this->em->persist($progress);
            return;
        }
        $progress
            ->setUpdatedAt(new \DateTime())
            ->setPercent($event->getPercent());
    }
}
