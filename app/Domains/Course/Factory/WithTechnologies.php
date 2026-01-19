<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Technology;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @mixin \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
trait WithTechnologies
{
    /**
     * @param  array<Technology>|null  $from
     */
    public function withTechnologies(int $count = 3, ?array $from = null): static
    {
        if ($from === null) {
            return $this->hasAttached(
                Technology::factory()->count($count),
                fn () => [
                    'version' => fake()->optional(0.5)->numerify('#.#'),
                    'primary' => fake()->boolean(70),
                ],
            );
        }

        return $this->afterCreating(function (Model $model) use ($count, $from) {
            /** @var Model&\App\Concerns\HasTechnologies $model */
            $technologies = collect(fake()->randomElements($from, fake()->numberBetween(1, $count)))
                ->mapWithKeys(fn (Technology $tech) => [
                    $tech->id => [
                        'version' => fake()->optional(0.5)->numerify('#.#'),
                        'primary' => fake()->boolean(70),
                    ],
                ]);
            $model->technologies()->attach($technologies);
        });
    }
}
