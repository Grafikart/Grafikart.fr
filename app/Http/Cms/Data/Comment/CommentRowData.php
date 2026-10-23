<?php

namespace App\Http\Cms\Data\Comment;

use App\Domains\Comment\Comment;
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

    public static function fromModel(Comment $comment): self
    {
        return new self(
            id: $comment->id,
            username: $comment->username ?? 'Utilisateur supprimé',
            email: $comment->email ?? $comment->user?->email ?? '',
            content: $comment->content,
            ip: $comment->ip,
            createdAt: $comment->created_at
        );
    }
}
