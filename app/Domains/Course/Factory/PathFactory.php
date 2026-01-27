<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Path;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'title' => fake()->unique()->sentence(rand(2, 5)),
            'description' => fake()->paragraphs(rand(1, 3), true),
        ];
    }
}
