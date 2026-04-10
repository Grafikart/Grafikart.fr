<?php

namespace App\Domains\Badge\Factory;

use App\Domains\Badge\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(),
            'position' => fake()->numberBetween(0, 100),
            'action' => fake()->randomElement(['comment', 'course', 'forum', 'login', 'premium']),
            'action_count' => fake()->numberBetween(1, 50),
            'theme' => fake()->randomElement(['grey', 'blue', 'green', 'orange', 'red']),
            'image' => fake()->optional()->imageUrl(128, 128),
            'unlockable' => fake()->boolean(),
        ];
    }

    public function unlockable(): static
    {
        return $this->state(fn () => [
            'unlockable' => true,
        ]);
    }
}
