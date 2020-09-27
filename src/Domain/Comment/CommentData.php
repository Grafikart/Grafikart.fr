<?php

namespace App\Domain\Comment;

abstract class CommentData
{
    public ?int $id = null;

    public ?string $username = null;

    public string $content = '';

    public ?string $avatar = null;

    public ?int $target = null;

    public ?string $email = null;

    public int $createdAt = 0;

    public ?int $parent = 0;

    public ?Comment $entity = null;

    public ?int $userId = null;
}
