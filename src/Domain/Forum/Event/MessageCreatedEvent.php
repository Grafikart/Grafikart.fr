<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Message;

class MessageCreatedEvent
{
    public function __construct(private Message $message)
    {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
