<?php

namespace App\Http\Cms\Data\Transaction;

use App\Component\ObjectMapper\Attribute\Map;
use DateTimeImmutable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class TransactionItemData
{
    public function __construct(
        public int $id,
        public float $price,
        public float $tax,
        public int $duration,
        public string $method,
        public bool $refunded,
        public DateTimeImmutable $createdAt,
        #[Map(source: 'author.id')]
        public int $userId,
        #[Map(source: 'author.username')]
        public string $username,
        #[Map(source: 'author.email')]
        public string $email,
    ) {
    }
}
