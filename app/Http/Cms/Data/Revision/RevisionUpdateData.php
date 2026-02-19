<?php

namespace App\Http\Cms\Data\Revision;

use App\Domains\Revision\RevisionStatus;
use Spatie\LaravelData\Data;

class RevisionUpdateData extends Data
{
    public function __construct(
        public readonly RevisionStatus $state,
        public readonly ?string $comment = null,
    ) {}
}
