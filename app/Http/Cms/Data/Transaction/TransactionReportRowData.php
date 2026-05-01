<?php

namespace App\Http\Cms\Data\Transaction;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TransactionReportRowData extends Data
{
    public function __construct(
        public readonly string $method,
        public readonly int $month,
        public readonly float $price,
        public readonly float $tax,
        public readonly float $fee,
    ) {}
}
