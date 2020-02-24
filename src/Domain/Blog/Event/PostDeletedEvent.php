<?php

namespace App\Domain\Blog\Event;

use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Blog\Post;

class PostDeletedEvent extends ContentDeletedEvent
{

    public function __construct(Post $content)
    {
        parent::__construct($content);
    }

}
