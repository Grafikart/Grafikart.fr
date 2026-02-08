<?php

namespace App\Domains\History\Factory;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\History\Progress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Progress>
 */
class ProgressFactory extends Factory
{
    protected $model = Progress::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'progressable_id' => Course::factory(),
            'progressable_type' => Course::class,
            'progress' => fake()->numberBetween(0, 1000),
        ];
    }

    public function forCourse(?Course $course = null): static
    {
        return $this->state(fn (array $attributes) => [
            'progressable_id' => $course?->id ?? Course::factory(),
            'progressable_type' => Course::class,
        ]);
    }

    public function forFormation(?Formation $formation = null): static
    {
        return $this->state(fn (array $attributes) => [
            'progressable_id' => $formation?->id ?? Formation::factory(),
            'progressable_type' => Formation::class,
        ]);
    }

    public function forUser(?User $user = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user?->id ?? User::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => 1000,
        ]);
    }

    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => fake()->numberBetween(1, 500),
        ]);
    }

    public function almostComplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => fake()->numberBetween(800, 999),
        ]);
    }
}
