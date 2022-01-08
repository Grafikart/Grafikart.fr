<?php

namespace App\Domain\Forum\Event;

use App\Domain\Forum\Entity\Message;

class PreMessageCreatedEvent
{
    public function __construct(private readonly Message $message)
    {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
