<?php

namespace App\Http\Cms\Data\Course;

use App\Domains\Course\Technology;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TechnologyUsageData extends Data
{
    public function __construct(
        public int $id,
        public string $name = '',
        public ?string $version = null,
        public bool $primary = false,
    ) {}

    /**
     * @param  Technology&object{pivot: object{version: ?string, primary: bool}}  $model
     */
    public static function fromModel(Technology $model)
    {
        return new self(
            id: $model->id,
            name: $model->name,
            version: $model->pivot->version,
            primary: $model->pivot->primary ?? true
        );
    }
}
