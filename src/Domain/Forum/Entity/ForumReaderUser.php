<?php

namespace App\Domain\Forum\Entity;

interface ForumReaderUser
{
    public function getForumReadTime(): ?\DateTimeInterface;
}
