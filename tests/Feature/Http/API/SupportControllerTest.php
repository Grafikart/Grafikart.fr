<?php

use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;
use App\Infrastructure\Notification\Notification\SupportQuestionCreatedNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

test('guests only receive published', function () {
    $course = Course::factory()->create();
    $publishedQuestion = SupportQuestion::factory()->for($course)->online()->create([
        'answer' => '**Réponse** avec [lien](https://grafikart.fr)',
    ]);
    SupportQuestion::factory()->for($course)->offline()->create([
        'answer' => 'Réponse privée',
    ]);

    $this->getJson("/api/courses/{$course->id}/support")
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $publishedQuestion->id);
});

test('it sends a queued email notification to the admin when creating a question', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $course = Course::factory()->free()->create([
        'duration' => 600,
    ]);

    $this->actingAs($user)
        ->postJson("/api/courses/{$course->id}/support", [
            'title' => 'Question sur ce chapitre',
            'content' => 'Je ne comprends pas cette partie du tutoriel.',
            'timestamp' => 120,
        ])
        ->assertCreated();

    $this->assertDatabaseHas('support_questions', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'title' => 'Question sur ce chapitre',
        'content' => 'Je ne comprends pas cette partie du tutoriel.',
        'timestamp' => 120,
        'online' => false,
    ]);

    Notification::assertSentTo($admin, function (SupportQuestionCreatedNotification $notification, array $channels) {
        return $channels === ['mail'] && $notification instanceof ShouldQueue;
    });
});

test('it does not send an admin notification when support question validation fails', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $course = Course::factory()->free()->create([
        'duration' => 600,
    ]);

    $this->actingAs($user)
        ->postJson("/api/courses/{$course->id}/support", [
            'title' => 'No',
            'content' => 'Je ne comprends pas cette partie du tutoriel.',
            'timestamp' => 120,
        ])
        ->assertUnprocessable();

    $this->assertDatabaseMissing('support_questions', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'content' => 'Je ne comprends pas cette partie du tutoriel.',
    ]);

    Notification::assertNotSentTo($admin, SupportQuestionCreatedNotification::class);
});
