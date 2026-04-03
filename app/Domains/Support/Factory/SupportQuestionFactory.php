<?php

namespace App\Domains\Support\Factory;

use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportQuestion>
 */
class SupportQuestionFactory extends Factory
{
    protected $model = SupportQuestion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(rand(3, 8)),
            'content' => fake()->paragraphs(rand(1, 4), true),
            'answer' => fake()->optional(0.4)->paragraphs(rand(1, 3), true),
            'online' => fake()->boolean(80),
            'course_id' => Course::factory(),
            'timestamp' => fake()->numberBetween(0, 7200),
        ];
    }

    public function online(): static
    {
        return $this->state(fn () => [
            'online' => true,
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn () => [
            'online' => false,
        ]);
    }
}
