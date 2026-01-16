<?php

namespace App\Domains\Premium\Factory;

use App\Domains\Premium\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $price = fake()->randomElement([500, 5000]);

        return [
            'user_id' => User::factory(),
            'price' => fake()->numberBetween(500, 5000),
            'duration' => $price === 500 ? 1 : 12,
            'tax' => round(fake()->randomElement([0, 0.2]) * $price),
            'method' => fake()->randomElement(['stripe', 'paypal']),
            'method_id' => fake()->uuid(),
            'refunded_at' => null,
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(),
            'fee' => round(0.015 * $price + 25),
            'created_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'refunded_at' => now(),
        ]);
    }
}
