<?php

namespace App\Http\Cms\Data\Course;

use App\Domains\Course\PathNode;
use Spatie\LaravelData\Attributes\Validation\Present;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathNodeData extends Data
{
    public function __construct(
        public int $id,
        public ?string $icon,
        public ?string $title,
        public ?string $description,
        public string $contentType,
        public ?int $contentId,
        public float $x,
        public float $y,
        public ?PathNodeMetaData $meta = null,
        /** @var PathNodeEdgeData[] */
        #[Present]
        public array $parents = [],
    ) {}

    public static function fromModel(PathNode $pathNode): self
    {
        return new self(
            id: $pathNode->id,
            icon: $pathNode->icon ?? '',
            title: $pathNode->title,
            description: $pathNode->description,
            contentType: $pathNode->content_type ?? '',
            contentId: $pathNode->content_id,
            meta: $pathNode->meta ? PathNodeMetaData::from($pathNode->meta) : null,
            x: $pathNode->x,
            y: $pathNode->y,
            parents: $pathNode->parents->map(
                fn (PathNode $p) => new PathNodeEdgeData(
                    id: $p->id,
                    primary: (bool) $p->pivot->primary,
                )
            )->all(),
        );
    }
}
