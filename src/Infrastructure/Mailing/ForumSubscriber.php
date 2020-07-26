<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\TopicService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumSubscriber implements EventSubscriberInterface
{
    private EmailFactory $emailFactory;
    private Mailer $mailer;
    private TopicService $topicService;

    public function __construct(
        Mailer $mailer,
        TopicService $topicService
    ) {
        $this->topicService = $topicService;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageCreatedEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageCreatedEvent $event): void
    {
        // On récupère les utilisateurs à notifier
        $users = $this->topicService->usersToNotify($event->getMessage());
        if (empty($users)) {
            return;
        }
        $author = $event->getMessage()->getAuthor();

        // On envoie les emails à chaque utilisateur
        foreach ($users as $user) {
            $isTopicOwner = $user->getId() === $event->getMessage()->getTopic()->getAuthor()->getId();
            $message = $this->mailer->createEmail('mails/forum/new_post.twig', [
                'author' => $author,
                'message' => $event->getMessage(),
                'topic' => $event->getMessage()->getTopic(),
                'is_topic_owner' => $isTopicOwner,
            ]);
            $message->subject($isTopicOwner ?
                "{$author->getUsername()} a répondu à votre sujet" :
                "{$author->getUsername()} a répondu à un sujet auquel vous avez participé");
            $message->to($user->getEmail());
            $this->mailer->send($message);
        }

        // On met à jour les status de notifications
        $this->topicService->updateNotificationStatusFor($event->getMessage(), $users);
    }
}
