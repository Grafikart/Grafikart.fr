<?php

namespace App\Domain\Revision\Event;

use App\Domain\Revision\Revision;

class RevisionRefusedEvent
{
    public function __construct(private readonly Revision $revision, private readonly string $comment)
    {
    }

    public function getRevision(): Revision
    {
        return $this->revision;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
