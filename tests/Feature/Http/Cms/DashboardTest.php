<?php

use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('cms.dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->admin()->create());

    $this->get(route('cms.dashboard'))->assertOk();
});

test('dashboard includes the latest unanswered support questions', function () {
    $this->actingAs(User::factory()->admin()->create());
    $course = Course::factory()->create();

    $latestQuestion = SupportQuestion::factory()->for($course)->create([
        'title' => 'Dernière question',
        'answer' => null,
        'created_at' => now(),
    ]);
    SupportQuestion::factory()->for($course)->create([
        'title' => 'Question déjà traitée',
        'answer' => 'Réponse',
        'created_at' => now()->subMinute(),
    ]);

    $this->get(route('cms.dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('supportQuestions.0.id', $latestQuestion->id)
            ->where('supportQuestions.0.answered', false)
        );
});
