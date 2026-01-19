<?php

namespace App\Http\Cms\Data\Comment;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CommentRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $email,
        public readonly string $content,
        public readonly string $ip,
        public readonly \DateTimeInterface $createdAt,
    ) {}
}
