<?php

namespace App\Domains\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Represent a Media attached to a property
 */
readonly class MediaProperty
{
    public function __construct(
        private string $property,
        private \Closure $namer,
        private string $disk = 'uploads',
    ) {}

    /**
     * Remove a media attached to an element
     */
    public function delete(Model $model): void
    {
        $fileName = $model->getAttribute($this->property);
        // No file attached to the media
        if (! $fileName) {
            return;
        }
        $dirname = pathinfo(($this->namer)($model), PATHINFO_DIRNAME);
        $fileName = $model->getAttribute($this->property);
        Storage::disk($this->disk)->delete(sprintf('%s/%s', $dirname, $fileName));
    }

    /**
     * Attach a media to the model
     */
    public function attach(Model $model, UploadedFile $file): string
    {
        // If the model is not persisted, delay the save at creation time
        if (! $model->exists) {
            $model::created(function ($model) use ($file) {
                $this->attach($model, $file);
                $model->save();
            });

            return '';
        }

        $path = ($this->namer)($model);
        $this->delete($model);
        $filename = sprintf('%s.%s', pathinfo($path, PATHINFO_FILENAME), $file->clientExtension());
        $file->storeAs(
            pathinfo($path, PATHINFO_DIRNAME),
            $filename,
            $this->disk
        );
        $model->setAttribute($this->property, $filename);

        return $filename;
    }
}
