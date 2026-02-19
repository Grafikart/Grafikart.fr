<?php

namespace App\Http\Cms\Data\Revision;

use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionStatus;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class RevisionShowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $content,
        public readonly string $currentContent,
        public readonly RevisionStatus $state,
    ) {}

    public static function fromModel(Revision $revision): self
    {
        return new self(
            id: $revision->id,
            content: $revision->content,
            currentContent: $revision->revisionable?->content ?? '',
            state: $revision->state,
        );
    }
}
