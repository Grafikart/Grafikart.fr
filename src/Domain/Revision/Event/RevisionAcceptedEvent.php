<?php

namespace App\Domain\Revision\Event;

use App\Domain\Revision\Revision;

class RevisionAcceptedEvent
{
    public function __construct(private Revision $revision)
    {
    }

    public function getRevision(): Revision
    {
        return $this->revision;
    }
}
