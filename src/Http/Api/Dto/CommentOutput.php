<?php

namespace App\Http\Api\Dto;

use App\Domain\Comment\Comment;

final class CommentOutput
{

    public ?int $id;

    public string $username;

    public string $content;

    public string $avatar;

    public int $createdAt;

    public int $parent;

    public function __construct(Comment $comment)
    {
        $this->id = $comment->getId();
        $this->username = $comment->getUsername();
        $this->content = $comment->getContent();
        $this->createdAt = $comment->getCreatedAt()->getTimestamp();
        $this->parent = $comment->getParent() ? $comment->getParent()->getId() : 0;
        $gravatar = md5($comment->getEmail());
        $this->avatar = "https://1.gravatar.com/avatar/{$gravatar}?s=200&r=pg&d=mp";
    }

}
