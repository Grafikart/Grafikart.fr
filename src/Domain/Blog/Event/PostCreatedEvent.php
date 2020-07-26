<?php

namespace App\Domain\Blog\Event;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Blog\Post;

class PostCreatedEvent extends ContentCreatedEvent
{
    public function __construct(Post $content)
    {
        parent::__construct($content);
    }
}
