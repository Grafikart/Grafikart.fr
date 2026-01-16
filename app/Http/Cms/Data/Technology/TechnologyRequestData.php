<?php

namespace App\Http\Cms\Data\Technology;

use App\Domains\Cms\DataToModel;
use App\Domains\Course\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class TechnologyRequestData extends Data implements DataToModel
{
    public function __construct(
        #[Required]
        #[Min(2)]
        public string $name,
        #[Required]
        #[Min(2)]
        public string $slug,
        public string $content,
        public ?string $type = null,
        public ?UploadedFile $imageFile = null,
        /** @var int[] */
        public array $requirements = [],
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Technology);

        // TODO : refactor this logic to put it inside a controller
        // Handle image upload
        if ($this->imageFile) {
            // Delete old image if exists
            if ($model->image) {
                Storage::disk('public')->delete('uploads/icons/'.$model->image);
            }

            // Store new image
            $filename = time().'_'.$this->imageFile->getClientOriginalName();
            $this->imageFile->storeAs('uploads/icons', $filename, 'public');
            $model->image = $filename;
        }

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
