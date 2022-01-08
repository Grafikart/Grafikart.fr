<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;

class TopicResolvedEvent
{
    public function __construct(private readonly Message $message)
    {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getTopic(): Topic
    {
        return $this->message->getTopic();
    }
}
