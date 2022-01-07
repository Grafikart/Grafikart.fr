<?php

namespace App\Domain\Application\Event;

use App\Domain\Application\Entity\Content;

class ContentDeletedEvent
{
    public function __construct(private Content $content)
    {
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
