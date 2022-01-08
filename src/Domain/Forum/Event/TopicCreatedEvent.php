<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Topic;

class TopicCreatedEvent
{
    public function __construct(private readonly Topic $topic)
    {
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }
}
