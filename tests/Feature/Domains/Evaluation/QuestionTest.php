<?php

use App\Domains\Course\Course;
use App\Domains\Evaluation\Question;
use App\Models\User;

function admin(): User
{
    return User::factory()->create(['name' => 'Grafikart']);
}

test('non-admin cannot access question endpoints', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $this->actingAs($user)
        ->getJson(route('cms.courses.questions.index', $course))
        ->assertForbidden();
});

test('admin can list questions for a course', function () {
    $course = Course::factory()->create();
    Question::factory()->count(3)->create(['course_id' => $course->id]);

    $this->actingAs(admin())
        ->getJson(route('cms.courses.questions.index', $course))
        ->assertSuccessful()
        ->assertJsonCount(3);
});

test('admin can create a choice question', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.store', $course), [
            'question' => 'Quelle est la capitale de la France ?',
            'type' => 'choice',
            'answer' => [
                'choices' => ['Paris', 'Lyon', 'Marseille'],
                'answer' => 0,
            ],
        ])
        ->assertCreated()
        ->assertJsonFragment(['question' => 'Quelle est la capitale de la France ?']);

    $this->assertDatabaseHas('questions', [
        'course_id' => $course->id,
        'question' => 'Quelle est la capitale de la France ?',
        'type' => 'choice',
    ]);
});

test('admin can create a text question', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.store', $course), [
            'question' => 'Quel est le mot clé pour déclarer une variable en PHP ?',
            'type' => 'text',
            'answer' => [
                'answer' => '$variable',
            ],
        ])
        ->assertCreated()
        ->assertJsonFragment(['type' => 'text']);
});

test('admin can update a question', function () {
    $question = Question::factory()->create();

    $this->actingAs(admin())
        ->putJson(route('cms.questions.update', $question), [
            'question' => 'Question modifiée',
            'type' => 'choice',
            'answer' => [
                'choices' => ['A', 'B', 'C'],
                'answer' => 1,
            ],
        ])
        ->assertSuccessful()
        ->assertJsonFragment(['question' => 'Question modifiée']);

    $this->assertDatabaseHas('questions', [
        'id' => $question->id,
        'question' => 'Question modifiée',
    ]);
});

test('admin can delete a question', function () {
    $question = Question::factory()->create();

    $this->actingAs(admin())
        ->deleteJson(route('cms.questions.destroy', $question))
        ->assertNoContent();

    $this->assertDatabaseMissing('questions', ['id' => $question->id]);
});

test('validation rejects missing question text', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.store', $course), [
            'question' => '',
            'type' => 'choice',
            'answer' => ['choices' => ['A', 'B'], 'answer' => 0],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['question']);
});

test('validation rejects missing answer', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.store', $course), [
            'question' => 'A valid question?',
            'type' => 'choice',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['answer']);
});

test('validation rejects invalid type', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.store', $course), [
            'question' => 'A valid question?',
            'type' => 'invalid',
            'answer' => ['answer' => 'test'],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('admin can import multiple questions', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.import', $course), [
            'questions' => [
                [
                    'question' => 'Question choix ?',
                    'type' => 'choice',
                    'answer' => ['choices' => ['A', 'B', 'C'], 'answer' => 1],
                ],
                [
                    'question' => 'Question texte ?',
                    'type' => 'text',
                    'answer' => ['answer' => 'réponse'],
                ],
            ],
        ])
        ->assertCreated()
        ->assertJsonCount(2);

    $this->assertDatabaseCount('questions', 2);
});

test('import validates each question', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.import', $course), [
            'questions' => [
                ['question' => '', 'type' => 'choice', 'answer' => ['choices' => ['A', 'B'], 'answer' => 0]],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['questions.0.question']);
});

test('import rejects empty questions array', function () {
    $course = Course::factory()->create();

    $this->actingAs(admin())
        ->postJson(route('cms.courses.questions.import', $course), [
            'questions' => [],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['questions']);
});
