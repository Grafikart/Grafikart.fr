<?php

use App\Domains\Course\Course;
use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionStatus;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->create(['content' => 'Original course content']);
});

describe('index', function () {
    it('lists user revisions', function () {
        Revision::factory()->count(3)->for($this->user)->for($this->course, 'revisionable')->create();
        Revision::factory()->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->get(route('revisions.index'))
            ->assertOk()
            ->assertViewHas('revisions', fn ($revisions) => $revisions->count() === 3);
    });

    it('requires authentication', function () {
        $this->get(route('revisions.index'))
            ->assertRedirect();
    });
});

describe('edit', function () {
    it('shows the revision form', function () {
        $this->actingAs($this->user)
            ->get(route('revision.edit', ['type' => 'course', 'id' => $this->course->id]))
            ->assertOk()
            ->assertViewHas('content', 'Original course content');
    });

    it('requires authentication', function () {
        $this->get(route('revision.edit', ['type' => 'course', 'id' => $this->course->id]))
            ->assertRedirect();
    });

    it('returns 404 for invalid type', function () {
        $this->actingAs($this->user)
            ->get('/revision/invalid/1')
            ->assertNotFound();
    });

    it('returns 404 for non-existent model', function () {
        $this->actingAs($this->user)
            ->get(route('revision.edit', ['type' => 'course', 'id' => 99999]))
            ->assertNotFound();
    });

    it('pre-fills content from pending revision', function () {
        Revision::factory()->for($this->user)->for($this->course, 'revisionable')->create([
            'content' => 'My draft revision',
            'state' => RevisionStatus::Pending,
        ]);

        $this->actingAs($this->user)
            ->get(route('revision.edit', ['type' => 'course', 'id' => $this->course->id]))
            ->assertOk()
            ->assertViewHas('content', 'My draft revision');
    });
});

describe('update', function () {
    it('creates a revision', function () {
        $this->actingAs($this->user)
            ->post(route('revision.update', ['type' => 'course', 'id' => $this->course->id]), [
                'content' => 'Updated course content here',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('revisions', [
            'user_id' => $this->user->id,
            'revisionable_id' => $this->course->id,
            'revisionable_type' => 'course',
            'content' => 'Updated course content here',
            'state' => RevisionStatus::Pending->value,
        ]);
    });

    it('validates content is required', function () {
        $this->actingAs($this->user)
            ->post(route('revision.update', ['type' => 'course', 'id' => $this->course->id]), [
                'content' => '',
            ])
            ->assertInvalid(['content']);
    });

    it('validates content minimum length', function () {
        $this->actingAs($this->user)
            ->post(route('revision.update', ['type' => 'course', 'id' => $this->course->id]), [
                'content' => 'short',
            ])
            ->assertInvalid(['content']);
    });

    it('requires authentication', function () {
        $this->post(route('revision.update', ['type' => 'course', 'id' => $this->course->id]), [
            'content' => 'Updated content here',
        ])->assertRedirect();
    });
});

describe('delete', function () {
    it('deletes own revision', function () {
        $revision = Revision::factory()->for($this->user)->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->delete(route('revision.delete', $revision))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('revisions', ['id' => $revision->id]);
    });

    it('cannot delete another user revision', function () {
        $otherUser = User::factory()->create();
        $revision = Revision::factory()->for($otherUser)->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->delete(route('revision.delete', $revision))
            ->assertForbidden();
    });

    it('requires authentication', function () {
        $revision = Revision::factory()->for($this->user)->for($this->course, 'revisionable')->create();

        $this->delete(route('revision.delete', $revision))
            ->assertRedirect();
    });
});
