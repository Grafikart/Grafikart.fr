<?php

namespace App\Domains\Revision\Factory;

use App\Domains\Course\Course;
use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Revision>
 */
class RevisionFactory extends Factory
{
    protected $model = Revision::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'revisionable_type' => Course::class,
            'revisionable_id' => Course::factory(),
            'content' => fake()->paragraphs(3, true),
            'state' => RevisionStatus::Pending,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RevisionStatus::Accepted,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RevisionStatus::Rejected,
        ]);
    }

    public function forCourse(): static
    {
        return $this->state(fn (array $attributes) => [
            'revisionable_type' => Course::class,
            'revisionable_id' => Course::factory(),
        ]);
    }
}
