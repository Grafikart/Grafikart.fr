<?php

namespace App\Concerns\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * @template TMedia of Model & RegisterMedia
 */
trait HasMedia
{
    /** @var MediaMapping[] */
    private static array $mediaProperties = [];

    /** @var callable[] */
    private static array $mediaDelayed = [];

    /**
     * Collect the media properties
     */
    private static function ensureMediaRegistered(): void
    {
        if (! static::$mediaProperties) {
            static::registerMedia();
        }
    }

    /**
     * Handle media deletion when model is deleted
     */
    protected static function bootHasMedia(): void
    {
        assert(is_subclass_of(static::class, RegisterMedia::class), 'The model has to implement RegisterMedia interface');
        static::deleting(function (RegisterMedia&Model $model) {
            $model->registerMedia();
            foreach (static::$mediaProperties as $property) {
                $property->delete($model);
            }
        });
    }

    /**
     * Register the media supported by the model
     */
    public static function registerMediaForProperty(
        string $property,
        string|callable $directory,
        string|callable|null $filename = null,
        string $disk = 'public',
        bool $needId = false,
    ): void {
        static::$mediaProperties[$property] = new MediaMapping(property: $property, directory: $directory, filename: $filename, disk: $disk, needId: $needId);
    }

    /**
     * Attach a file to the model
     */
    public function attachMedia(?UploadedFile $file, string $property): static
    {
        if (! $file) {
            return $this;
        }
        assert($this instanceof RegisterMedia);
        static::ensureMediaRegistered();

        $mapping = static::$mediaProperties[$property] ?? null;
        if (! $mapping) {
            throw new \RuntimeException(sprintf('The property %s on %s has no media registered', $property, static::class));
        }

        assert($this instanceof Model);
        if ($this->exists || ! $mapping->needId) {
            $mapping->attach($this, $file);
        } else {
            if (empty(static::$mediaDelayed)) {
                static::created(function () {
                    foreach (static::$mediaDelayed as $delayed) {
                        $delayed();
                    }
                    static::$mediaDelayed = [];
                });
            }
            static::$mediaDelayed[] = function () use ($mapping, $file) {
                $mapping->attach($this, $file);
            };
        }

        return $this;
    }

    /**
     * Resolve the url for an attached file
     */
    public function mediaUrl(string $property): ?string
    {
        if (! $this->getAttribute($property)) {
            return null;
        }
        static::ensureMediaRegistered();

        $mapping = static::$mediaProperties[$property] ?? null;
        if (! $mapping) {
            throw new \RuntimeException(sprintf('The property %s on %s has no media registered', $property, static::class));
        }

        return $mapping->url($this);
    }
}
