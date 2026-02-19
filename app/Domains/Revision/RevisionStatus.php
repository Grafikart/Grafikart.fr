<?php

namespace App\Domains\Revision;

enum RevisionStatus: int
{
    case Rejected = -1;
    case Pending = 0;
    case Accepted = 1;
}
