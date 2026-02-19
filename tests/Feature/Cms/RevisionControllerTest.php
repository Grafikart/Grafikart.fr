<?php

use App\Domains\Course\Course;
use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionStatus;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Grafikart']);
    $this->course = Course::factory()->create(['content' => 'Original content']);
});

describe('index', function () {
    it('lists pending revisions by default', function () {
        Revision::factory()->count(3)->for($this->course, 'revisionable')->create();
        Revision::factory()->accepted()->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->get(route('cms.revisions.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('revisions/index')
                ->where('state', 'pending')
            );
    });

    it('filters by state', function () {
        Revision::factory()->count(2)->for($this->course, 'revisionable')->create();
        Revision::factory()->accepted()->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->get(route('cms.revisions.index', ['state' => 'accepted']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('revisions/index')
                ->where('state', 'accepted')
            );
    });
});

describe('show', function () {
    it('returns revision data', function () {
        $revision = Revision::factory()->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->get(route('cms.revisions.show', $revision))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('revisions/show')
                ->has('revision')
            );
    });
});

describe('update', function () {
    it('accepts a revision and updates target content', function () {
        $revision = Revision::factory()->for($this->course, 'revisionable')->create([
            'content' => 'New proposed content',
        ]);

        $this->actingAs($this->user)
            ->post(route('cms.revisions.update', $revision), [
                'state' => RevisionStatus::Accepted->value,
                'comment' => 'Looks good',
            ])
            ->assertRedirect(route('cms.revisions.index'))
            ->assertSessionHas('success');

        $revision->refresh();
        expect($revision->state)->toBe(RevisionStatus::Accepted);
        expect($revision->comment)->toBe('Looks good');

        $this->course->refresh();
        expect($this->course->content)->toBe('New proposed content');
    });

    it('rejects a revision without updating content', function () {
        $revision = Revision::factory()->for($this->course, 'revisionable')->create([
            'content' => 'New proposed content',
        ]);

        $this->actingAs($this->user)
            ->post(route('cms.revisions.update', $revision), [
                'state' => RevisionStatus::Rejected->value,
                'comment' => 'Not relevant',
            ])
            ->assertRedirect(route('cms.revisions.index'))
            ->assertSessionHas('success');

        $revision->refresh();
        expect($revision->state)->toBe(RevisionStatus::Rejected);
        expect($revision->comment)->toBe('Not relevant');

        $this->course->refresh();
        expect($this->course->content)->toBe('Original content');
    });

    it('validates state is required', function () {
        $revision = Revision::factory()->for($this->course, 'revisionable')->create();

        $this->actingAs($this->user)
            ->post(route('cms.revisions.update', $revision), [])
            ->assertInvalid(['state']);
    });
});
