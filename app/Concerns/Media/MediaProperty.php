<?php

namespace App\Concerns\Media;

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
        private string|\Closure $directory,
        private string|\Closure $filename,
        private string $disk = 'uploads',
    ) {}

    private function getDirectory(Model $model): string
    {
        return is_string($this->directory)
            ? $this->directory
            : ($this->directory)($model);
    }

    private function getFilename(Model $model): string
    {
        return is_string($this->filename)
            ? $model->getAttribute($this->filename)
            : ($this->filename)($model);
    }

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
        $directory = $this->getDirectory($model);
        Storage::disk($this->disk)->delete(sprintf('%s/%s', $directory, $fileName));
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

        $this->delete($model);
        $filename = sprintf('%s.%s', $this->getFilename($model), $file->clientExtension());
        $file->storeAs(
            $this->getDirectory($model),
            $filename,
            $this->disk
        );
        $model->setAttribute($this->property, $filename);

        return $filename;
    }
}
