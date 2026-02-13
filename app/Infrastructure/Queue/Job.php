<?php

namespace App\Infrastructure\Queue;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $name
 * @property-read ?object $job
 */
class Job extends Model
{
    public $timestamps = false;

    protected $table = 'jobs';

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'reserved_at' => 'immutable_datetime:U',
            'available_at' => 'immutable_datetime:U',
            'created_at' => 'immutable_datetime:U',
        ];
    }

    /**
     * @return Attribute<string, never>
     */
    public function name(): Attribute
    {
        return Attribute::get(function (): string {
            /** @var string $result */
            $result = collect(explode('\\', $this->payload['data']['commandName'] ?? ''))->last();

            return $result;
        });
    }

    public function job(): Attribute
    {
        return Attribute::get(function (): ?object {
            try {
                if (! isset($this->payload['data']['command'])) {
                    return null;
                }
                $job = unserialize($this->payload['data']['command']);

                return is_object($job) ? $job : null;
            } catch (\Exception) {
                return null;
            }
        }
        );
    }
}
