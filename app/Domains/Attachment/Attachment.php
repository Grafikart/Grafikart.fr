<?php

namespace App\Domains\Attachment;

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\WithMedia;
use App\Domains\Attachment\Factory\AttachmentFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property CarbonImmutable $created_at
 */
class Attachment extends Model implements HasMedia
{
    /** @use HasFactory<\App\Domains\Attachment\Factory\AttachmentFactory> */
    use HasFactory;

    use WithMedia;

    protected $fillable = [
        'name',
        'size',
        'attachable_id',
        'attachable_type'
    ];

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

    public function registerMedia(): void
    {
        $this->registerMediaForProperty(
            property: 'name',
            directory: fn (self $attachment) => sprintf('attachments/%d', $attachment->created_at->year),
        );
    }

    protected static function newFactory(): AttachmentFactory
    {
        return AttachmentFactory::new();
    }
}
