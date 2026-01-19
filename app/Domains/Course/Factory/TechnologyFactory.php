<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    private static int $sequenceIndex = 0;

    /**
     * @var array<int, array{name: string, type: string}>
     */
    private const KNOWN_TECHNOLOGIES = [
        ['name' => 'PHP', 'type' => 'language'],
        ['name' => 'JavaScript', 'type' => 'language'],
        ['name' => 'TypeScript', 'type' => 'language'],
        ['name' => 'Python', 'type' => 'language'],
        ['name' => 'Ruby', 'type' => 'language'],
        ['name' => 'Go', 'type' => 'language'],
        ['name' => 'Rust', 'type' => 'language'],
        ['name' => 'Java', 'type' => 'language'],
        ['name' => 'Laravel', 'type' => 'framework'],
        ['name' => 'Symfony', 'type' => 'framework'],
        ['name' => 'React', 'type' => 'library'],
        ['name' => 'Vue.js', 'type' => 'framework'],
        ['name' => 'Angular', 'type' => 'framework'],
        ['name' => 'Next.js', 'type' => 'framework'],
        ['name' => 'Nuxt', 'type' => 'framework'],
        ['name' => 'Node.js', 'type' => 'tool'],
        ['name' => 'Docker', 'type' => 'tool'],
        ['name' => 'Git', 'type' => 'tool'],
        ['name' => 'MySQL', 'type' => 'tool'],
        ['name' => 'PostgreSQL', 'type' => 'tool'],
        ['name' => 'Redis', 'type' => 'tool'],
        ['name' => 'Tailwind CSS', 'type' => 'library'],
        ['name' => 'Bootstrap', 'type' => 'library'],
        ['name' => 'WordPress', 'type' => 'tool'],
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $index = self::$sequenceIndex++;

        if ($index < count(self::KNOWN_TECHNOLOGIES)) {
            $tech = self::KNOWN_TECHNOLOGIES[$index];
            $name = $tech['name'];
            $type = $tech['type'];
        } else {
            $name = fake()->word().uniqid();
            $type = fake()->randomElement(['language', 'framework', 'library', 'tool']);
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'content' => fake()->paragraphs(rand(2, 4), true),
            'image' => null,
            'type' => $type,
        ];
    }

    public static function resetSequence(): void
    {
        self::$sequenceIndex = 0;
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'test-icon.png',
        ]);
    }
}
