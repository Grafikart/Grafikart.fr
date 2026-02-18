<?php

use App\Domains\Course\Course;
use App\Domains\Evaluation\Question;
use App\Models\User;

it('returns questions for a premium user', function () {
    $user = User::factory()->premium()->create();
    $course = Course::factory()->create();
    $questions = Question::factory()->count(3)->create(['course_id' => $course->id]);

    $response = $this->actingAs($user)->getJson("/api/courses/{$course->id}/questions");

    $response->assertSuccessful();
    $response->assertJsonCount(3);
    $response->assertJsonFragment(['question' => $questions->first()->question]);
});

it('returns 403 for non-premium user', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    Question::factory()->create(['course_id' => $course->id]);

    $response = $this->actingAs($user)->getJson("/api/courses/{$course->id}/questions");

    $response->assertForbidden();
});

it('returns 401 for unauthenticated user', function () {
    $course = Course::factory()->create();
    Question::factory()->create(['course_id' => $course->id]);

    $response = $this->getJson("/api/courses/{$course->id}/questions");

    $response->assertUnauthorized();
});

it('returns empty array for course without questions', function () {
    $user = User::factory()->premium()->create();
    $course = Course::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/courses/{$course->id}/questions");

    $response->assertSuccessful();
    $response->assertJsonCount(0);
});
