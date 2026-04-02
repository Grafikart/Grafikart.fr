<?php

namespace App\Http\Cms\Data\Course;

use App\Concerns\AfterPersist;
use App\Domains\Cms\DataToModel;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class PathRequestData extends Data implements DataToModel
{
    use AfterPersist;

    public function __construct(
        public string $title = '',
        public string $slug = '',
        public ?\DateTimeImmutable $createdAt = null,
        public bool $online = false,
        public string $description = '',
        public string $tags = '',
        /** @var PathNodeData[] */
        public array $nodes = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (isset($properties['nodes']) && is_string($properties['nodes'])) {
            $properties['nodes'] = json_decode($properties['nodes'], true);
        }
        $properties['tags'] = collect(explode(',', $properties['tags'] ?? ''))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->implode(', ');

        return $properties;
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Path);

        $model->fill([
            'title' => $this->title,
            'slug' => $this->slug,
            'created_at' => $this->createdAt ?? now(),
            'online' => $this->online,
            'description' => empty($this->description) ? null : $this->description,
            'tags' => empty($this->tags) ? null : $this->tags,
        ]);

        $this->afterPersist($model, function (Path $path): void {
            $nodes = collect($this->nodes);
            // Delete detached nodes (existing nodes no longer in payload)
            $nodeIds = $nodes->pluck('id');
            foreach ($path->nodes as $node) {
                if (! $nodeIds->contains($node->id)) {
                    $node->delete();
                }
            }
            $nodesById = $path->nodes->keyBy('id');

            // Create/Update nodes, building an ID map (negative → real)
            $idMap = [];
            foreach ($this->nodes as $nodeData) {
                $attributes = [
                    'icon' => $nodeData->icon,
                    'title' => $nodeData->title,
                    'description' => $nodeData->description,
                    'content_type' => $nodeData->contentType,
                    'content_id' => $nodeData->contentId,
                    'meta' => self::normalizeMeta($nodeData->meta),
                    'x' => $nodeData->x,
                    'y' => $nodeData->y,
                ];

                if ($nodeData->id > 0 && isset($nodesById[$nodeData->id])) {
                    $nodesById[$nodeData->id]->update($attributes);
                    $idMap[$nodeData->id] = $nodeData->id;
                } else {
                    $node = $path->nodes()->create($attributes);
                    $idMap[$nodeData->id] = $node->id;
                }
            }

            // Sync parent relationships with pivot data (resolving negative IDs via idMap)
            foreach ($this->nodes as $nodeData) {
                $realId = $idMap[$nodeData->id];
                $realParents = collect($nodeData->parents)->mapWithKeys(
                    fn (PathNodeEdgeData $p) => [($idMap[$p->id] ?? $p->id) => ['primary' => $p->primary]]
                );
                PathNode::find($realId)->parents()->sync($realParents->all());
            }
        });

        return $model;
    }

    private static function normalizeMeta(?PathNodeMetaData $meta): ?array
    {
        if ($meta === null) {
            return null;
        }

        $normalizedMeta = array_filter($meta->toArray(), fn (mixed $value): bool => $value !== null && $value !== '');

        return $normalizedMeta === [] ? null : $normalizedMeta;
    }
}
