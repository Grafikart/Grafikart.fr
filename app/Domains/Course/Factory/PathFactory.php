<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Path;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Path>
 */
class PathFactory extends Factory
{
    protected $model = Path::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(2, 5));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(rand(1, 3), true),
            'tags' => fake()->words(rand(2, 4), true),
            'online' => fake()->boolean(90),
            'created_at' => fake()->dateTimeBetween('-2 years'),
        ];
    }

    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => true,
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => false,
        ]);
    }
}
