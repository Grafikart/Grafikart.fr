<?php

namespace App\Domain\History\Event;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;

class ProgressEvent
{

    private Content $content;
    private int $percent;
    private User $user;

    public function __construct(Content $content, User $user, int $percent)
    {
        $this->content = $content;
        $this->percent = $percent;
        $this->user = $user;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getPercent(): int
    {
        return $this->percent;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
