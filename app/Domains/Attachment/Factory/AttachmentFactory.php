<?php

namespace App\Domains\Attachment\Factory;

use App\Domains\Attachment\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->uuid().'.'.fake()->randomElement(['jpg', 'png', 'webp']),
            'size' => fake()->numberBetween(1000, 5000000),
            'created_at' => fake()->dateTimeBetween('-2 years'),
        ];
    }
}
