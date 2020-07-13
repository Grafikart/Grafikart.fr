<?php

namespace App\Domain\Blog\Event;

use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Blog\Post;

class PostUpdatedEvent extends ContentUpdatedEvent
{
    public function __construct(Post $content)
    {
        parent::__construct($content);
    }
}
