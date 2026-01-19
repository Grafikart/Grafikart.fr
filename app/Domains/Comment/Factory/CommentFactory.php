<?php

namespace App\Domains\Comment\Factory;

use App\Domains\Blog\Post;
use App\Domains\Comment\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasUser = fake()->boolean(70);

        return [
            'user_id' => $hasUser ? User::factory() : null,
            'commentable_type' => Post::class,
            'commentable_id' => Post::factory(),
            'email' => $hasUser ? null : fake()->email(),
            'username' => $hasUser ? null : fake()->userName(),
            'content' => fake()->paragraphs(rand(1, 3), true),
            'ip' => fake()->ipv4(),
        ];
    }

    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'email' => null,
            'username' => null,
        ]);
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'email' => fake()->email(),
            'username' => fake()->userName(),
        ]);
    }
}
