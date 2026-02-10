<?php

namespace App\Concerns\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handle media attached to a property for a model
 */
readonly class MediaMapping
{
    public function __construct(
        private string $property,
        private string|\Closure $directory,
        private string|\Closure|null $filename = null,
        private string $disk = 'public',
        public bool $needId = false,
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
            : ($this->filename)($model, $file);
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

    public function path(Model $model): ?string
    {
        $fileName = $model->getAttribute($this->property);
        if (! $fileName) {
            return null;
        }
        $directory = $this->getDirectory($model);

        return Storage::disk($this->disk)->path(sprintf('%s/%s', $directory, $fileName));
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
        if ($this->needId && ! $model->exists) {
            throw new \RuntimeException(sprintf('Cannot attach file to a non existing model %s', $model));
        }

        $filename = sprintf('%s.%s', $this->getFilename($model, $file), $file->clientExtension());
        try {
            $file->storeAs(
                $this->getDirectory($model),
                $filename,
                $this->disk
            );
            // The computed filename if different from the previous one
            if ($filename !== $model->getAttribute($this->property)) {
                $this->delete($model);
            }
            $model->setAttribute($this->property, $filename);
        } catch (\Exception $e) {
            // if the file is not writable just do nothing
        }

        return $filename;
    }
}
