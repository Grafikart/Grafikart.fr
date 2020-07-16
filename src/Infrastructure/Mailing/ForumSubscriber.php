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
        $users = $this->topicService->usersToNotify($event->getMessage());
        if (empty($users)) {
            return;
        }
        $author = $event->getMessage()->getAuthor();
        foreach ($users as $user) {
            $message = $this->mailer->createEmail('mails/forum/new_post.twig', [
                'author' => $author,
                'message' => $event->getMessage(),
                'topic' => $event->getMessage()->getTopic(),
                'is_topic_owner' => true, // TODO : ajouter la logique ici
            ]);
            $message->subject("{$author->getUsername()} a répondu à votre sujet");
            $message->to($user->getEmail());
            $this->mailer->send($message);
        }
    }
}
