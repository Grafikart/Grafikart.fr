<?php

namespace App\Domains\Revision\Event;

use App\Domains\Revision\Revision;

readonly class RejectedRevisionEvent
{
    public function __construct(public Revision $revision) {}
}
