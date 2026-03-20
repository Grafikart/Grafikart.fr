<?php

use App\Domains\Course\Course;
use App\Domains\Evaluation\Question;
use App\Domains\Evaluation\QuizService;
use App\Domains\History\Progress;
use App\Models\User;

it('detects when the quiz was already completed by the user', function () {
    $user = User::factory()->create();
    $course = Course::factory()->online()->create();
    Question::factory()->for($course)->create();

    Progress::factory()
        ->forUser($user)
        ->forCourse($course)
        ->create([
            'progressable_type' => $course->getMorphClass(),
            'score' => 100,
        ]);

    expect(app(QuizService::class)->isCompleted($course, $user))->toBeTrue();
});

it('does not mark the quiz as completed when no score exists', function () {
    $user = User::factory()->create();
    $course = Course::factory()->online()->create();
    Question::factory()->for($course)->create();

    Progress::factory()
        ->forUser($user)
        ->forCourse($course)
        ->create([
            'progressable_type' => $course->getMorphClass(),
            'score' => null,
        ]);

    expect(app(QuizService::class)->isCompleted($course, $user))->toBeFalse();
});
