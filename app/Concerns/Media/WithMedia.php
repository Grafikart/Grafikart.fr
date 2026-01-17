<?php

namespace App\Concerns\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @template TMedia of Model
 */
trait WithMedia
{
    /** @var MediaProperty[] */
    public array $mediaProperties = [];

    /**
     * Handle media deletion when model is deleted
     */
    protected static function bootWithMedia(): void
    {
        static::deleting(function (HasMedia&Model $model) {
            $model->registerMedia();
            foreach ($model->mediaProperties as $property) {
                $property->delete($model);
            }
        });
    }

    /**
     * Register the media supported by the model
     */
    public function registerMediaForProperty(
        string               $property,
        string|callable      $directory,
        string|callable|null $filename = null,
        string               $disk = 'uploads',
    ): void
    {
        // A media is already registered for this property
        if (array_key_exists($property, $this->mediaProperties)) {
            return;
        }
        $this->mediaProperties[$property] = new MediaProperty($property, $directory, $filename, $disk);
    }

    /**
     * Attach a file to the model
     */
    public function attachMedia(?UploadedFile $file, string $property): static
    {
        if (!$file) {
            return $this;
        }
        assert($this instanceof HasMedia);
        $this->registerMedia();

        $mapping = $this->mediaProperties[$property] ?? null;
        if (!$mapping) {
            throw new \RuntimeException(sprintf('The property %s on %s has no media registered', $property, static::class));
        }

        assert($this instanceof Model);
        $mapping->attach($this, $file);

        return $this;
    }

    public function mediaUrl(string $property): ?string
    {
        if (!$this->getAttribute($property)) {
            return null;
        }
        assert($this instanceof HasMedia);
        $this->registerMedia();

        $mapping = $this->mediaProperties[$property] ?? null;
        if (!$mapping) {
            throw new \RuntimeException(sprintf('The property %s on %s has no media registered', $property, static::class));
        }
        return $mapping->url($this);
    }
}
