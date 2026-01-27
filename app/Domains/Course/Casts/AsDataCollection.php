<?php

namespace App\Domains\Course\Casts;

use App\Domains\Course\Chapter;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsDataCollection implements Castable
{
    public static function castUsing(array $arguments)
    {
        $itemClass = $arguments[0];

        return new readonly class($itemClass) implements CastsAttributes
        {
            public function __construct(
                public string $itemClass,
            ) {}

            /**
             * @return \Illuminate\Support\Collection<Chapter>
             */
            public function get(Model $model, string $key, mixed $value, array $attributes): \Illuminate\Support\Collection
            {
                return collect(($this->itemClass)::collect(json_decode($value, true)));
            }

            /**
             * Prepare the given value for storage.
             *
             * @param  array<string, mixed>  $attributes
             */
            public function set(Model $model, string $key, mixed $value, array $attributes): mixed
            {
                foreach ($value as $row) {
                    assert($row instanceof $this->itemClass, sprintf('Cannot cast items, expect %s', $this->itemClass));
                }

                return collect($value)->toJson();
            }
        };
    }

    public static function of(string $className): string
    {
        return static::class.':'.$className;
    }
}
