<?php

namespace App\Domain\Application\Event;

use App\Domain\Application\Entity\Content;

class ContentUpdatedEvent
{
    public function __construct(private readonly Content $content, private readonly Content $previous)
    {
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getPrevious(): Content
    {
        return $this->previous;
    }
}
