<?php

namespace App\Domains\Evaluation\Factory;

use App\Domains\Course\Course;
use App\Domains\Evaluation\Question;
use App\Domains\Evaluation\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'question' => fake()->sentence(),
            'type' => QuestionType::Choice,
            'answer' => [
                'choices' => ['Paris', 'Lyon', 'Marseille'],
                'answer' => 0,
            ],
        ];
    }

    public function choice(): static
    {
        return $this->state(fn () => [
            'type' => QuestionType::Choice,
            'answer' => [
                'choices' => ['Paris', 'Lyon', 'Marseille'],
                'answer' => 0,
            ],
        ]);
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'type' => QuestionType::Text,
            'answer' => [
                'answer' => fake()->word(),
            ],
        ]);
    }
}
