<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\DifficultyLevel;
use App\Domains\Course\Formation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Formation>
 */
class FormationFactory extends Factory
{
    use WithTechnologies;

    protected $model = Formation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(3, 6));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(rand(3, 10), true),
            'online' => fake()->boolean(90),
            'attachment_id' => null,
            'short' => fake()->optional(0.7)->paragraph(),
            'chapters' => [],
            'youtube_playlist' => fake()->optional(0.3)->regexify('PL[A-Za-z0-9_-]{32}'),
            'links' => fake()->optional(0.3)->url(),
            'level' => fake()->randomElement(DifficultyLevel::cases())->value,
            'deprecated_by_id' => null,
            'force_redirect' => false,
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

    /**
     * @param  array<array{title: string, content: int[]}>  $chapters
     */
    public function withChapters(array $chapters): static
    {
        return $this->state(fn (array $attributes) => [
            'chapters' => $chapters,
        ]);
    }
}
