<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Course;
use App\Domains\Course\DifficultyLevel;
use App\Domains\Course\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(3, 8));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(rand(3, 10), true),
            'online' => fake()->boolean(90),
            'attachment_id' => null,
            'youtube_thumbnail_id' => null,
            'deprecated_by_id' => null,
            'duration' => fake()->numberBetween(0, 7200),
            'youtube_id' => fake()->optional(0.7)->regexify('[A-Za-z0-9_-]{11}'),
            'video_path' => null,
            'source' => null,
            'demo' => fake()->optional(0.3)->url(),
            'premium' => fake()->boolean(20),
            'level' => fake()->randomElement(DifficultyLevel::cases())->value,
            'force_redirect' => false,
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

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'premium' => true,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'premium' => false,
        ]);
    }

    public function junior(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => DifficultyLevel::Junior->value,
        ]);
    }

    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => DifficultyLevel::Intermediaire->value,
        ]);
    }

    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => DifficultyLevel::Senior->value,
        ]);
    }

    public function withTechnologies(int $count = 3): static
    {
        return $this->hasAttached(
            Technology::factory()->count($count),
            fn () => [
                'version' => fake()->optional(0.5)->numerify('#.#'),
                'primary' => fake()->boolean(70),
            ],
        );
    }
}
