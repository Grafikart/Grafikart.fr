<?php

namespace App\Concerns;

use App\Domains\Course\Technology;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasTechnologies
{
    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)
            ->withPivot(['version', 'primary']);
    }
}
