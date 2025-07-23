<?php

namespace App\Infrastructure\Spam\Subscriber;

use App\Domain\Forum\Event\PreMessageCreatedEvent;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Infrastructure\Spam\SpamService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SpamSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly SpamService $spamService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreTopicCreatedEvent::class => 'checkTopic',
            PreMessageCreatedEvent::class => 'checkMessage',
        ];
    }

    public function checkTopic(PreTopicCreatedEvent $topicCreatedEvent): void
    {
        $topic = $topicCreatedEvent->getTopic();
        $content = (string) $topic->getContent();
        foreach ($this->spamService->words() as $word) {
            if (false !== stripos($content, (string) $word)) {
                $topic->setSpam(true);

                return;
            }
        }
    }

    public function checkMessage(PreMessageCreatedEvent $messageCreatedEvent): void
    {
        $message = $messageCreatedEvent->getMessage();
        $content = (string) $message->getContent();
        foreach ($this->spamService->words() as $word) {
            if (false !== stripos($content, (string) $word)) {
                $message->setSpam(true);

                return;
            }
        }
    }
}
