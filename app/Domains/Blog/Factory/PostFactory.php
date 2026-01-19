<?php

namespace App\Domains\Blog\Factory;

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

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
            'category_id' => BlogCategory::factory(),
            'attachment_id' => null,
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
