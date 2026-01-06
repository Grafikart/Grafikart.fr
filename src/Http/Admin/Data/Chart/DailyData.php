<?php

namespace App\Http\Admin\Data\Chart;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class DailyData
{

    public function __construct(
        public string $date,
        public int $value,
    ){

    }

}
