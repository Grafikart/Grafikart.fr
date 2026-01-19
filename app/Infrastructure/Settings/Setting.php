<?php

namespace App\Infrastructure\Settings;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Find the setting for a specific key
     */
    public function findKey(string $key): ?string
    {
        return self::find($key)?->value;
    }

    /**
     * Retrieve all the settings indexed by key
     */
    public static function findAll(): array
    {
        return self::newQuery()
            ->get()
            ->keyBy('key')
            ->map(fn (Setting $s) => $s->value)
            ->values()
            ->toArray();
    }
}
