<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Message;

class PreMessageCreatedEvent
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
