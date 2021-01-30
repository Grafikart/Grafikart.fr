<?php

namespace App\Infrastructure\Spam\Subscriber;

use App\Domain\Forum\Event\PreMessageCreatedEvent;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Helper\OptionManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SpamSubscriber implements EventSubscriberInterface
{
    private OptionManagerInterface $optionManager;

    public function __construct(
        OptionManagerInterface $optionManager
    ) {
        $this->optionManager = $optionManager;
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
        foreach ($this->getSpamWords() as $word) {
            if (false !== stripos($content, $word)) {
                $topic->setSpam(true);

                return;
            }
        }
    }

    public function checkMessage(PreMessageCreatedEvent $messageCreatedEvent): void
    {
        $message = $messageCreatedEvent->getMessage();
        $content = (string) $message->getContent();
        foreach ($this->getSpamWords() as $word) {
            if (false !== stripos($content, $word)) {
                $message->setSpam(true);

                return;
            }
        }
    }

    private function getSpamWords(): array
    {
        $spamWords = $this->optionManager->get('spam_words');
        if (null === $spamWords) {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', $spamWords) ?: [];
    }
}
