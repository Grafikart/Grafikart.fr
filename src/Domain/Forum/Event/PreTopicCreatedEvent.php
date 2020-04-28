<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Topic;

class PreTopicCreatedEvent
{

    private Topic $topic;

    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

}
