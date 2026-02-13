<?php

namespace App\Http\Cms\Data;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SearchResultData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
        public readonly string $url,
    ) {}

    public static function fromModel(Model $model): self
    {
        return match (true) {
            $model instanceof Course => new self(
                id: $model->id,
                name: $model->title,
                type: 'course',
                url: route('cms.courses.edit', [$model->id]),
            ),
            $model instanceof Post => new self(
                id: $model->id,
                name: $model->title,
                type: 'post',
                url: route('cms.posts.edit', [$model->id]),
            ),
            $model instanceof Technology => new self(
                id: $model->id,
                name: $model->name,
                type: 'technology',
                url: route('cms.technologies.edit', [$model->id]),
            ),
            $model instanceof Formation => new self(
                id: $model->id,
                name: $model->title,
                type: 'formation',
                url: route('cms.formations.edit', [$model->id]),
            ),
            $model instanceof User => new self(
                id: $model->id,
                name: $model->name,
                type: 'user',
                url: route('cms.users.index'),
            ),
            default => throw new \Exception('Ce contenu n\'est pas géré'),
        };
    }
}
