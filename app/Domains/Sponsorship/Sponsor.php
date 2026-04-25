<?php

namespace App\Domains\Sponsorship;

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\RegisterMedia;
use App\Domains\Sponsorship\Factory\SponsorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model implements RegisterMedia
{
    /** @use HasFactory<SponsorFactory> */
    use HasFactory;

    use HasMedia;

    protected $fillable = [
        'name',
        'url',
        'content',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => SponsorType::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): SponsorFactory
    {
        return SponsorFactory::new();
    }

    public static function registerMedia(): void
    {
        self::registerMediaForProperty(
            property: 'logo',
            directory: 'sponsors',
            filename: 'name',
        );
    }
}
