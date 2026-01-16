<?php

namespace App\Http\Cms\Data\Transaction;

use App\Domains\Premium\Models\Transaction;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TransactionRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $duration,
        public readonly int $price,
        public readonly int $tax,
        public readonly int $fee,
        public readonly string $method,
        public readonly bool $refunded,
        public readonly \DateTimeInterface $createdAt,
        public readonly int $userId,
        public readonly string $username,
        public readonly string $email,
    ) {}

    public static function fromModel(Transaction $transaction): self
    {
        return new self(
            id: $transaction->id,
            duration: $transaction->duration,
            price: $transaction->price,
            tax: $transaction->tax,
            fee: $transaction->fee,
            method: $transaction->method,
            refunded: $transaction->isRefunded(),
            createdAt: $transaction->created_at,
            userId: $transaction->user_id,
            username: $transaction->user->name,
            email: $transaction->user->email,
        );
    }
}
