<?php

use App\Domains\Course\Course;
use App\Domains\History\Progress;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->create();
});

it('saves progress with score', function () {
    $this->actingAs($this->user)
        ->postJson("/api/courses/{$this->course->id}/progress", [
            'progress' => 500,
            'score' => 75,
        ])
        ->assertSuccessful();

    $progress = Progress::query()->first();
    expect($progress->progress)->toBe(500)
        ->and($progress->score)->toBe(75);
});

it('saves score only without progress', function () {
    $this->actingAs($this->user)
        ->postJson("/api/courses/{$this->course->id}/progress", [
            'score' => 80,
        ])
        ->assertSuccessful();

    $progress = Progress::query()->first();
    expect($progress->score)->toBe(80);
});

it('validates score range 0-100', function () {
    $this->actingAs($this->user)
        ->postJson("/api/courses/{$this->course->id}/progress", [
            'score' => 150,
        ])
        ->assertUnprocessable();
});

it('validates progress range 0-1000', function () {
    $this->actingAs($this->user)
        ->postJson("/api/courses/{$this->course->id}/progress", [
            'progress' => 1500,
        ])
        ->assertUnprocessable();
});

it('returns 401 for unauthenticated user', function () {
    $this->postJson("/api/courses/{$this->course->id}/progress", [
        'progress' => 500,
    ])->assertUnauthorized();
});
