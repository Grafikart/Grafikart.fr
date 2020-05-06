<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Message;

class MessageCreatedEvent
{

    private Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

}
