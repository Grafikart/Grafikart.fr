<?php

namespace App\Domain\School\DTO;

use App\Domain\School\Entity\School;

class SchoolPreprocessResult
{

    public function __construct(
        /** @var SchoolImportRow[] */
        public readonly array  $rows,
        public readonly School $school,
        public readonly string $csv = '',
    ) {
    }

    public function getMonths(): int
    {
        return array_sum(array_map(fn (SchoolImportRow $row) => $row->months, $this->rows));
    }
}
