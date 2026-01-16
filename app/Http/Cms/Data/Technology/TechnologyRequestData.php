<?php

namespace App\Http\Cms\Data\Technology;

use App\Domains\Cms\DataToModel;
use App\Domains\Course\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class TechnologyRequestData extends Data implements DataToModel
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $content,
        public ?string $type = null,
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
            'imageFile' => ['nullable', 'file', 'image:allow_svg'],
            'requirements' => ['array', 'exists:technologies,id'],
        ];
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Technology);

        $model->attachMedia($this->imageFile, 'image');
        $model->fill([
            'name' => $this->name,
            'slug' => $this->slug,
            'content' => $this->content,
            'type' => $this->type,
        ]);

        // Save the model first to get an ID
        if (! $model->exists) {
            $model->save();
        }

        // Sync requirements after model is saved
        $model->requirements()->sync($this->requirements);

        return $model;
    }
}
