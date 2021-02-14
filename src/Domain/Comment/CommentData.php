<?php

namespace App\Domain\Comment;

abstract class CommentData
{
    private ?int $id = null;

    private ?string $username = null;

    private string $content = '';

    private ?string $avatar = null;

    private ?int $target = null;

    private ?string $email = null;

    private int $createdAt = 0;

    private ?int $parent = 0;

    private ?Comment $entity = null;

    private ?int $userId = null;
}
