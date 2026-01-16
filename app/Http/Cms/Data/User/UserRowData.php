<?php

namespace App\Http\Cms\Data\User;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $email,
        public readonly \DateTimeInterface $createdAt,
        public readonly bool $isPremium,
        public readonly bool $isBanned,
        public readonly ?string $lastLoginIp,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            username: $user->name,
            email: $user->email,
            createdAt: $user->created_at,
            isPremium: $user->isPremium(),
            isBanned: $user->trashed(),
            lastLoginIp: $user->last_login_ip,
        );
    }
}
