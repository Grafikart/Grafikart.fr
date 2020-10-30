<?php

namespace App\Infrastructure\Spam\Subscriber;

use App\Core\OptionManagerInterface;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
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

    private function getSpamWords(): array
    {
        $spamWords = $this->optionManager->get('spam_words');
        if (null === $spamWords) {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', $spamWords) ?: [];
    }
}
