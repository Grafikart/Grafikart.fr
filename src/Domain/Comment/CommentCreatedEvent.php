<?php

namespace App\Domain\Comment;

class CommentCreatedEvent
{

    /**
     * @var Comment
     */
    private Comment $comment;

    public function __construct(Comment $comment)
    {

        $this->comment = $comment;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
