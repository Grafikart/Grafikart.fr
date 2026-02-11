<?php

namespace App\Domains\Premium\Factory;

use App\Domains\Premium\Models\Plan;
use App\Domains\Premium\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'state' => Subscription::ACTIVE,
            'next_payment' => fake()->dateTimeBetween('now', '+1 year'),
            'stripe_id' => 'sub_'.fake()->unique()->regexify('[A-Za-z0-9]{24}'),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => Subscription::INACTIVE,
        ]);
    }
}
