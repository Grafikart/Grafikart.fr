<?php

namespace App\Domain\Forum;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\ReadTime;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Domain\Forum\Event\TopicCreatedEvent;
use App\Domain\Forum\Repository\ReadTimeRepository;
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

    /**
     * Crée un nouveau sujet
     */
    public function createTopic(Topic $topic): void
    {
        $topic->setCreatedAt(new \DateTime());
        $topic->setUpdatedAt(new \DateTime());
        $this->dispatcher->dispatch(new PreTopicCreatedEvent($topic));
        $this->em->persist($topic);
        $this->em->flush();
        $this->dispatcher->dispatch(new TopicCreatedEvent($topic));
    }

    /**
     * Met à jour un sujet
     */
    public function updateTopic(Topic $topic): void
    {
        $topic->setUpdatedAt(new \DateTime());
        $this->em->flush();
    }

    /**
     * Marque un sujet comme lu
     */
    public function readTopic(Topic $topic, User $user)
    {
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        $repository->updateReadTimeForTopic($topic, $user);
        $this->em->flush();
    }

    /**
     * Récupère les dates de dernières lectures pour les sujet indiqués
     *
     * @param Topic[] $topics
     * @return int[]
     */
    public function readTimes(array $topics, ?User $user): array
    {
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        return array_map(function (ReadTime $t) {
            return $t->getTopic()->getId();
        }, $repository->findByTopicsAndUser($topics, $user));
    }
}
