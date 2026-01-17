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
        private string|\Closure|null $filename = null,
        private string $disk = 'uploads',
    ) {}

    private function getDirectory(Model $model): string
    {
        return is_string($this->directory)
            ? $this->directory
            : ($this->directory)($model);
    }

    private function getFilename(Model $model, UploadedFile $file): string
    {
        if (! $this->filename) {
            return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return is_string($this->filename)
            ? $model->getAttribute($this->filename)
            : ($this->filename)($model);
    }

    public function url(Model $model): ?string
    {
        $fileName = $model->getAttribute($this->property);
        if (! $fileName) {
            return null;
        }
        $directory = $this->getDirectory($model);

        return Storage::disk($this->disk)->url(sprintf('%s/%s', $directory, $fileName));
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
    public function attach(Model $model, UploadedFile $file, bool $force = false): string
    {
        // If the model is not persisted, delay the save at creation time
        if (! $model->exists && $force === false) {
            $model::creating(function (Model $m) use ($file, $model) {
                if ($m->is($model)) {
                    $this->attach($m, $file, true);
                }
            });

            return '';
        }

        $this->delete($model);
        $filename = sprintf('%s.%s', $this->getFilename($model, $file), $file->clientExtension());
        $file->storeAs(
            $this->getDirectory($model),
            $filename,
            $this->disk
        );
        $model->setAttribute($this->property, $filename);

        return $filename;
    }
}
