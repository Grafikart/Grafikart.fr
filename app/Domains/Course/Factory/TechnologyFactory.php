<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    private static int $nameIndex = 0;

    private const NAMES = [
        'PHP',
        'JavaScript',
        'TypeScript',
        'NodeJS',
        'React',
        'Vue.js',
        'Angular',
        'Laravel',
        'Symfony',
        'Python',
        'Ruby',
        'Go',
        'Rust',
        'Docker',
        'Kubernetes',
        'MySQL',
        'PostgreSQL',
        'Redis',
        'MongoDB',
        'GraphQL',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = self::NAMES[self::$nameIndex++ % count(self::NAMES)];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'content' => fake()->paragraphs(rand(2, 4), true),
            'image' => null,
            'type' => fake()->randomElement(['language', 'framework', 'library', 'tool']),
        ];
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'test-icon.png',
        ]);
    }
}
