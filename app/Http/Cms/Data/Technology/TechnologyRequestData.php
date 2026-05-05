<?php

namespace App\Http\Cms\Data\Technology;

use App\Concerns\AfterPersist;
use App\Domains\Cms\DataToModel;
use App\Domains\Course\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class TechnologyRequestData extends Data implements DataToModel
{
    use AfterPersist;

    public function __construct(
        public string $name,
        public string $slug,
        public string $content,
        public ?string $type = null,
        public ?int $deprecatedById = null,
        public ?UploadedFile $imageFile = null,
        /** @var int[] */
        public array $requirements = [],
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'slug' => ['required', 'string', 'min:2'],
            'content' => ['required', 'string'],
            'type' => ['nullable', 'string'],
            'deprecatedById' => ['nullable', 'integer', 'exists:technologies,id'],
            'imageFile' => ['nullable', 'file', 'image:allow_svg'],
            'requirements' => ['array', 'exists:technologies,id'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Technology);

        $model->fill([
            'name' => $this->name,
            'slug' => $this->slug,
            'content' => $this->content,
            'type' => $this->type,
            'deprecated_by_id' => $this->deprecatedById,
        ]);
        $model->attachMedia($this->imageFile, 'image');

        $this->afterPersist($model, fn () => $model->requirements()->sync($this->requirements));

        return $model;
    }
}
