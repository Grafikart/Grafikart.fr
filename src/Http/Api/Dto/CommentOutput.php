<?php

namespace App\Http\Api\Dto;

use App\Domain\Comment\Comment;

final class CommentOutput
{

    public ?int $id;

    public string $username;

    public string $content;

    public string $avatar;

    public function __construct(Comment $comment)
    {
        $this->id = $comment->getId();
        $this->username = $comment->getUsername();
        $this->content = $comment->getContent();
        $gravatar = md5($comment->getEmail());
        $this->avatar = "https://1.gravatar.com/avatar/{$gravatar}?s=200&r=pg&d=mp";
    }

}
