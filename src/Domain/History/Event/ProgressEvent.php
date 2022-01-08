<?php

namespace App\Domain\History\Event;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;

class ProgressEvent
{
    public function __construct(private readonly Content $content, private readonly User $user, private readonly float $progress)
    {
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
