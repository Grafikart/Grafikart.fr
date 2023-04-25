<?php

namespace App\Domain\Comment;

use App\Domain\Comment\Entity\Comment;

class CommentCreatedEvent
{
    public function __construct(private readonly Comment $comment)
    {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
