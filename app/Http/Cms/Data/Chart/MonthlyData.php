<?php

namespace App\Http\Cms\Data\Chart;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class MonthlyData
{
    public function __construct(
        public int $month,
        public int $year,
        public int $value,
    ) {}
}
