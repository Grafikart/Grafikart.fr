<?php

namespace App\Infrastructure\Queue;

/**
 * @property-read ?string $name
 * @property-read ?object $job
 */
class FailedJob extends Job
{
    protected $table = 'failed_jobs';

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'failed_at' => 'immutable_datetime',
        ];
    }
}
