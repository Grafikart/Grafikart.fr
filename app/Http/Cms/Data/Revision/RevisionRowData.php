<?php

namespace App\Http\Cms\Data\Revision;

use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionStatus;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RevisionRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $authorName,
        public readonly string $targetTitle,
        public readonly string $targetType,
        public readonly RevisionStatus $state,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    public static function fromModel(Revision $revision): self
    {
        return new self(
            id: $revision->id,
            authorName: $revision->user?->name ?? 'Utilisateur supprimé',
            targetTitle: $revision->revisionable?->title ?? 'Contenu supprimé',
            targetType: $revision->revisionable_type,
            state: $revision->state,
            createdAt: $revision->created_at,
        );
    }
}
