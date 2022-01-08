<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ForumMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageCreatedEvent::class => ['onMessageCreated'],
        ];
    }

    public function onMessageCreated(MessageCreatedEvent $event): void
    {
        $message = $event->getMessage();
        if ($message->isSpam()) {
            return;
        }
        $topic = $message->getTopic();

        /** @var Message[]|Topic[] $messages */
        $messages = collect($topic->getMessages()->toArray())
            ->push($topic)
            ->filter(fn ($v) => $v->getAuthor() !== $message->getAuthor())
            ->keyBy(function (object $v) {
                /** @var Message|Topic $v */
                $author = $v->getAuthor();

                return $author->getId();
            });

        $userName = htmlentities($message->getAuthor()->getUsername());
        $topicName = htmlentities($topic->getName());

        foreach ($messages as $message) {
            if ($message instanceof Topic) {
                $wording = '%s a répondu à votre sujet %s';
            } elseif ($message instanceof Message && $message->hasNotification()) {
                $wording = '%s a participé au sujet %s';
            } else {
                $wording = null;
            }
            if (null !== $wording) {
                $this->service->notifyUser(
                    $message->getAuthor(),
                    sprintf($wording, "<strong>{$userName}</strong>", "« $topicName »"),
                    $message,
                );
            }
        }
    }
}
