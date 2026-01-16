<?php

namespace App\Http\Cms\Data\Transaction;

use DateTimeImmutable;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TransactionRowData extends Data
{
    public function __construct(
        readonly public int               $id,
        readonly public float             $price,
        readonly public float             $tax,
        readonly public int               $duration,
        readonly public string            $method,
        readonly public bool              $refunded,
        readonly public DateTimeImmutable $createdAt,
        readonly public int               $userId,
        readonly public string            $username,
        readonly public string            $email,
    ) {
    }
}
