<?php

namespace App\Domains\Sponsorship\Factory;

use App\Domains\Sponsorship\Sponsor;
use App\Domains\Sponsorship\SponsorType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsor>
 */
class SponsorFactory extends Factory
{
    protected $model = Sponsor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'url' => fake()->url(),
            'content' => fake()->paragraph(),
            'type' => fake()->randomElement(SponsorType::cases())->value,
        ];
    }

    public function affiliation(): static
    {
        return $this->state(fn () => [
            'type' => SponsorType::Affiliation->value,
        ]);
    }

    public function sponsor(): static
    {
        return $this->state(fn () => [
            'type' => SponsorType::Sponsor->value,
        ]);
    }
}
