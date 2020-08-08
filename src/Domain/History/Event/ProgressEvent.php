<?php

namespace App\Domain\History\Event;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;

class ProgressEvent
{
    private Content $content;
    private float $progress;
    private User $user;

    public function __construct(Content $content, User $user, float $progress)
    {
        $this->content = $content;
        $this->progress = $progress;
        $this->user = $user;
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
