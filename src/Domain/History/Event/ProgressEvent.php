<?php

namespace App\Domain\History\Event;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;

class ProgressEvent
{
    private Content $content;
    private int $progress;
    private User $user;

    public function __construct(Content $content, User $user, int $progress)
    {
        $this->content = $content;
        $this->progress = $progress;
        $this->user = $user;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
