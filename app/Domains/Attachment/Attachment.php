<?php

namespace App\Domains\Attachment;

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\RegisterMedia;
use App\Domains\Attachment\Factory\AttachmentFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property CarbonImmutable $created_at
 */
class Attachment extends Model implements RegisterMedia
{
    /** @use HasFactory<AttachmentFactory> */
    use HasFactory;

    use HasMedia;

    protected $fillable = [
        'name',
        'size',
        'attachable_id',
        'attachable_type',
    ];

    public function url(?int $width = null, ?int $height = null): string
    {
        return $this->mediaUrl('name', $width, $height);
    }

    // Attachments are never updated
    public function getUpdatedAtColumn()
    {
        return null;
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    public static function registerMedia(): void
    {
        self::registerMediaForProperty(
            property: 'name',
            directory: fn (self $attachment) => sprintf('attachments/%d', $attachment->created_at->year),
        );
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): AttachmentFactory
    {
        return AttachmentFactory::new();
    }
}
