<?php

namespace App\Domain\Comment;

class CommentCreatedEvent
{
    public function __construct(private Comment $comment)
    {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
