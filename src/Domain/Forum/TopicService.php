<?php

namespace App\Domain\Forum;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\ReadTime;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Domain\Forum\Event\TopicCreatedEvent;
use App\Domain\Forum\Event\TopicResolvedEvent;
use App\Domain\Forum\Repository\ReadTimeRepository;
use App\Domain\Forum\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class TopicService
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher, private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Crée un nouveau sujet.
     */
    public function createTopic(Topic $topic): void
    {
        $topic->setCreatedAt(new \DateTimeImmutable());
        $topic->setUpdatedAt(new \DateTimeImmutable());
        $this->dispatcher->dispatch(new PreTopicCreatedEvent($topic));
        $this->em->persist($topic);
        $this->em->flush();
        $this->dispatcher->dispatch(new TopicCreatedEvent($topic));
    }

    /**
     * Met à jour un sujet.
     */
    public function updateTopic(Topic $topic): void
    {
        $topic->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    /**
     * Marque un sujet comme lu.
     */
    public function readTopic(Topic $topic, User $user): void
    {
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        $repository->updateReadTimeForTopic($topic, $user);
        $this->em->flush();
    }

    /**
     * Marque tous les sujets du forum comme lu.
     */
    public function readAllTopics(User $user): void
    {
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        $repository->deleteAllForUser($user);
        $user->setForumReadTime(new \DateTimeImmutable());
        $this->em->flush();
    }

    /**
     * Récupère les dates de dernières lectures pour les sujet indiqués.
     *
     * @param Topic[] $topics
     *
     * @return array<int|null>
     */
    public function getReadTopicsIds(array $topics, ?User $user): array
    {
        if (null === $user) {
            return [];
        }
        $ids = [];
        $unreadTopics = $topics;

        // On extrait tous les sujet ayant une date inférieur à celle de l'utilisateur
        if ($user->getForumReadTime()) {
            $unreadTopics = [];
            foreach ($topics as $topic) {
                if ($topic->getUpdatedAt() <= $user->getForumReadTime()) {
                    $ids[] = $topic->getId();
                } else {
                    $unreadTopics[] = $topic;
                }
            }
        }

        if (empty($unreadTopics)) {
            return $ids;
        }

        // Pour les sujets restants, on regarde la table de lecture des sujets
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        $readTimes = $repository->findReadByTopicsAndUser($unreadTopics, $user);
        foreach ($readTimes as $readTime) {
            $ids[] = $readTime->getTopic()->getId();
        }

        return $ids;
    }

    /**
     * Récupère la liste des utilisateur à notifier en lien avec un message.
     *
     * @return User[]
     */
    public function usersToNotify(Message $message): array
    {
        /** @var TopicRepository $repository */
        $repository = $this->em->getRepository(Topic::class);

        return $repository->findUsersToNotify($message);
    }

    /**
     * Indique que les utilisateurs ont été notifié par email.
     *
     * @param User[] $users
     */
    public function updateNotificationStatusFor(Message $message, array $users): void
    {
        /** @var ReadTimeRepository $repository */
        $repository = $this->em->getRepository(ReadTime::class);
        $repository->updateNotificationStatusForUsers($message->getTopic(), $users);
    }

    /**
     * Marque le message comme résolution du topic.
     */
    public function messageSolveTopic(Message $message): void
    {
        $message->setAccepted(true);
        $message->getTopic()->setSolved(true);
        $this->dispatcher->dispatch(new TopicResolvedEvent($message));
        $this->em->flush();
    }

    /**
     * Définit si l'utilisateur est abonné ou non au topic.
     */
    public function isUserSubscribedToTopic(Topic $topic, ?User $user): ?bool
    {
        if (null === $user || $user->getId() === $topic->getAuthor()->getId()) {
            return null;
        }
        $notification = null;
        foreach ($topic->getMessages() as $message) {
            if ($message->getAuthor()->getId() === $user->getId()) {
                $notification = $message->hasNotification();
            }
        }

        return $notification;
    }
}
