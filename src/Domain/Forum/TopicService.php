<?php

namespace App\Domain\Forum;

use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Domain\Forum\Event\TopicCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TopicService
{
    private EventDispatcherInterface $dispatcher;
    private EntityManagerInterface $em;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    public function createTopic(Topic $topic): void
    {
        $topic->setCreatedAt(new \DateTime());
        $topic->setUpdatedAt(new \DateTime());
        $this->dispatcher->dispatch(new PreTopicCreatedEvent($topic));
        $this->em->persist($topic);
        $this->em->flush();
        $this->dispatcher->dispatch(new TopicCreatedEvent($topic));
    }

    public function updateTopic(Topic $topic): void
    {
        $topic->setUpdatedAt(new \DateTime());
        $this->em->persist($topic);
        $this->em->flush();
    }
}
