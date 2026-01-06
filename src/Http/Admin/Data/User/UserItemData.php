<?php

namespace App\Http\Admin\Data\User;

use App\Component\ObjectMapper\Attribute\Map;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use DateTimeImmutable;

#[TypeScript]
readonly class UserItemData
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public DateTimeImmutable $createdAt,
        public bool $isPremium,
        #[Map(source: 'bannedAt', transform: 'boolval')]
        public bool $isBanned,
        public ?string $lastLoginIp,
    ) {
    }
}
