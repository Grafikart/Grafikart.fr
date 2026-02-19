<?php

namespace App\Concerns;

use App\Domains\Revision\Revision;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRevisions
{
    /** @return MorphMany<Revision, $this> */
    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable');
    }
}
