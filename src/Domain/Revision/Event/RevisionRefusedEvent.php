<?php

namespace App\Domain\Revision\Event;

use App\Domain\Revision\Revision;

class RevisionRefusedEvent
{

    private Revision $revision;

    public function __construct(Revision $revision)
    {
        $this->revision = $revision;
    }

    public function getRevision(): Revision
    {
        return $this->revision;
    }

}
