<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Topic;

class PreTopicCreatedEvent
{
    public function __construct(private Topic $topic)
    {
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }
}
