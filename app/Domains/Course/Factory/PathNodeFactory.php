<?php

namespace App\Domains\Course\Factory;

use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PathNode>
 */
class PathNodeFactory extends Factory
{
    protected $model = PathNode::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path_id' => Path::factory(),
            'icon' => fake()->optional(0.7)->randomElement(['code', 'database', 'server', 'layout', 'terminal', 'git-branch']),
            'title' => fake()->unique()->sentence(rand(2, 5)),
            'description' => fake()->paragraphs(rand(1, 2), true),
            'content_type' => null,
            'content_id' => null,
            'x' => fake()->randomFloat(2, 0, 1000),
            'y' => fake()->randomFloat(2, 0, 1000),
        ];
    }

    public function withParents(int $count = 1): static
    {
        return $this->afterCreating(function (PathNode $node) use ($count) {
            $parents = PathNode::factory()->count($count)->create([
                'path_id' => $node->path_id,
            ]);
            $node->parents()->attach($parents);
        });
    }
}
