<?php

namespace App\Domain\Forum\Entity;

interface ForumReaderUserInterface
{
    public function getForumReadTime(): ?\DateTimeInterface;
}
