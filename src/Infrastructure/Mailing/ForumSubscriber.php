<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\TopicService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class ForumSubscriber implements EventSubscriberInterface
{
    private EmailFactory $emailFactory;
    private MailerInterface $mailer;
    private TopicService $topicService;

    public function __construct(
        EmailFactory $emailFactory,
        MailerInterface $mailer,
        TopicService $topicService
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->topicService = $topicService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageCreatedEvent::class => 'onMessage',
        ];
    }

    public function onMessage(MessageCreatedEvent $event): void
    {
        $users = $this->topicService->usersToNotify($event->getMessage()->getTopic());
        if (empty($users)) {
            return;
        }
        $author = $event->getMessage()->getAuthor();
        foreach ($users as $user) {
            $message = $this->emailFactory->makeFromTemplate('mails/forum/new_post.html.twig', [
                'author' => $author,
                'topic' => $event->getMessage()->getTopic(),
                'is_topic_owner' => true, // TODO : ajouter la logique ici
            ]);
            $message->subject("{$author->getUsername()} a répondu à votre sujet");
            $message->to($user->getEmail());
            $this->mailer->send($message);
        }
    }
}
