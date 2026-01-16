<?php

namespace App\Domains\Premium\Factory;

use App\Domains\Premium\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Mensuel', 'Trimestriel', 'Annuel', 'Premium', 'Basique']),
            'price' => fake()->numberBetween(5, 100),
            'duration' => fake()->randomElement([1, 3, 6, 12]),
            'stripe_id' => 'price_'.fake()->unique()->regexify('[A-Za-z0-9]{24}'),
        ];
    }
}
