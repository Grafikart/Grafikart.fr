<?php

namespace App\Domain\Revision\Event;

use App\Domain\Revision\Revision;

class RevisionSubmittedEvent
{
    public function __construct(private readonly Revision $revision)
    {
    }

    public function getRevision(): Revision
    {
        return $this->revision;
    }
}
