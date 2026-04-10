<?php

namespace App\Domains\School\Data;

use Carbon\CarbonImmutable;

final readonly class SchoolStudentData
{
    public function __construct(
        public string $email,
        public CarbonImmutable $createdAt,
        public CarbonImmutable $endAt,
        public int $completions,
    ) {}

}
