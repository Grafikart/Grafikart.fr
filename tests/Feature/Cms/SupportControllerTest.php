<?php

use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Domains\Support\SupportQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Event;

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
    it('updates an existing support question', function () {
        Event::fake();

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
    });

    it('normalizes blank answers to null', function () {
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
