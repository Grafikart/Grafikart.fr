<?php

namespace App\Domains\Coupon\Factory;

use App\Domains\Coupon\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'id' => strtoupper(fake()->unique()->bothify('COUPON-####-??')),
            'school_id' => null,
            'user_id' => null,
            'claimed_at' => null,
            'email' => fake()->safeEmail(),
            'months' => fake()->numberBetween(1, 24),
        ];
    }

    public function claimed(): static
    {
        return $this->state(fn () => [
            'claimed_at' => fake()->dateTimeBetween('-6 months'),
        ]);
    }
}
