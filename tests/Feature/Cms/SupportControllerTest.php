<?php

use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Domains\Notification\Models\Notification as UserNotification;
use App\Domains\Support\Event\SupportQuestionAnswered;
use App\Domains\Support\SupportQuestion;
use App\Infrastructure\Mailing\Notification\SupportQuestionAnsweredNotification;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->course = Course::factory()->create(['duration' => 600]);
    $this->question = SupportQuestion::factory()->for($this->course)->create([
        'title' => 'Question initiale',
        'content' => 'Contenu initial',
        'answer' => null,
        'online' => false,
        'timestamp' => 60,
    ]);
    $this->author = $this->question->user;
    $this->validData = [
        'title' => 'Question mise à jour',
        'content' => 'Contexte mis à jour',
        'answer' => "## Réponse\n\nUne réponse utile",
        'online' => true,
        'courseId' => $this->course->id,
        'timestamp' => 300,
    ];
});

describe('index', function () {
    it('paginates support questions', function () {
        SupportQuestion::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.support.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('support/index')
                ->where('pagination.data.0.id', SupportQuestion::query()->latest('id')->first()->id)
            );
    });
});

describe('edit', function () {
    it('displays the edit form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.support.edit', $this->question))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('support/form')
                ->where('item.id', $this->question->id)
            );
    });
});

describe('update', function () {
    it('updates an existing support question and dispatches the first answer event', function () {
        Event::fake([
            ContentUpdatedEvent::class,
            SupportQuestionAnswered::class,
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), $this->validData)
            ->assertRedirect(route('cms.support.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('support_questions', [
            'id' => $this->question->id,
            'title' => 'Question mise à jour',
            'content' => 'Contexte mis à jour',
            'answer' => "## Réponse\n\nUne réponse utile",
            'online' => true,
            'course_id' => $this->course->id,
            'timestamp' => 300,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
        Event::assertDispatched(SupportQuestionAnswered::class, function (SupportQuestionAnswered $event) {
            return $event->question->is($this->question);
        });
    });

    it('normalizes blank answers to null', function () {
        Event::fake([SupportQuestionAnswered::class]);

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), [
                ...$this->validData,
                'answer' => '   ',
            ])
            ->assertRedirect(route('cms.support.index'));

        $this->assertDatabaseHas('support_questions', [
            'id' => $this->question->id,
            'answer' => null,
        ]);

        Event::assertNotDispatched(SupportQuestionAnswered::class);
    });

    it('does not dispatch the support answered event when the question already had an answer', function () {
        Event::fake([SupportQuestionAnswered::class]);

        $this->question->update([
            'answer' => 'Réponse existante',
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), $this->validData)
            ->assertRedirect(route('cms.support.index'));

        Event::assertNotDispatched(SupportQuestionAnswered::class);
    });

    it('sends an email to the author when answering a question for the first time', function () {
        Notification::fake();

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), $this->validData)
            ->assertRedirect(route('cms.support.index'));

        Notification::assertSentTo($this->author, function (SupportQuestionAnsweredNotification $notification, array $channels) {
            return $channels === ['mail'];
        });
    });

    it('does not send an email when updating an already answered question', function () {
        Notification::fake();

        $this->question->update([
            'answer' => 'Réponse existante',
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), $this->validData)
            ->assertRedirect(route('cms.support.index'));

        Notification::assertNotSentTo($this->author, SupportQuestionAnsweredNotification::class);
    });

    it('does not create a notification when updating an already answered question', function () {
        $existingUrl = route('courses.show', ['slug' => $this->course->slug, 'course' => $this->course]).'#support';

        $this->question->update([
            'answer' => 'Réponse existante',
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.support.update', $this->question), $this->validData)
            ->assertRedirect(route('cms.support.index'));

        $notifications = UserNotification::query()
            ->where('user_id', $this->author->id)
            ->where('url', $existingUrl)
            ->count();

        expect($notifications)->toBe(0);
    });
});

describe('destroy', function () {
    it('deletes a support question', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->delete(route('cms.support.destroy', $this->question))
            ->assertRedirect(route('cms.support.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('support_questions', [
            'id' => $this->question->id,
        ]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
