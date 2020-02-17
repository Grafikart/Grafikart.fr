<?php

namespace App\Domain\Application\Event;

use App\Domain\Application\Entity\Content;

class ContentUpdatedEvent
{

    private Content $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

}
