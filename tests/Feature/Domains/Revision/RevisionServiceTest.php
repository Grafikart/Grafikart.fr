<?php

use App\Domains\Course\Course;
use App\Domains\Revision\Event\AcceptedRevisionEvent;
use App\Domains\Revision\Event\RejectedRevisionEvent;
use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionService;
use App\Domains\Revision\RevisionStatus;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->create(['content' => 'Original content']);
    $this->service = app(RevisionService::class);
});

describe('sendRevision', function () {
    it('creates a revision for the authenticated user', function () {
        $this->actingAs($this->user);

        $revision = $this->service->sendRevision($this->course, 'New content');

        expect($revision)->toBeInstanceOf(Revision::class);
        expect($revision->user_id)->toBe($this->user->id);
        expect($revision->content)->toBe('New content');
        expect($revision->revisionable_type)->toBe('course');
        expect($revision->revisionable_id)->toBe($this->course->id);
        expect($revision->state)->toBe(RevisionStatus::Pending);
    });
});

describe('getContentForUser', function () {
    it('returns target content when no pending revision exists', function () {
        $this->actingAs($this->user);

        $content = $this->service->getContentForUser($this->course);

        expect($content)->toBe('Original content');
    });

    it('returns pending revision content when one exists', function () {
        $this->actingAs($this->user);

        Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Draft content', 'state' => RevisionStatus::Pending]);

        $content = $this->service->getContentForUser($this->course);

        expect($content)->toBe('Draft content');
    });

    it('ignores accepted revisions', function () {
        $this->actingAs($this->user);

        Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->accepted()
            ->create(['content' => 'Accepted content']);

        $content = $this->service->getContentForUser($this->course);

        expect($content)->toBe('Original content');
    });

    it('ignores revisions from other users', function () {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        Revision::factory()
            ->for($otherUser)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Other user draft']);

        $content = $this->service->getContentForUser($this->course);

        expect($content)->toBe('Original content');
    });

    it('returns latest pending revision when multiple exist', function () {
        $this->actingAs($this->user);

        Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Old draft', 'created_at' => now()->subDay()]);

        Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Latest draft', 'created_at' => now()]);

        $content = $this->service->getContentForUser($this->course);

        expect($content)->toBe('Latest draft');
    });
});

describe('accept', function () {
    it('updates target content and sets state to accepted', function () {
        $revision = Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Proposed content']);

        $this->service->accept($revision, 'Looks good');

        $revision->refresh();
        expect($revision->state)->toBe(RevisionStatus::Accepted);
        expect($revision->comment)->toBe('Looks good');

        $this->course->refresh();
        expect($this->course->content)->toBe('Proposed content');
    });

    it('dispatches AcceptedRevisionEvent', function () {
        Event::fake([AcceptedRevisionEvent::class]);

        $revision = Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create();

        $this->service->accept($revision);

        Event::assertDispatched(AcceptedRevisionEvent::class, fn ($e) => $e->revision->id === $revision->id);
    });

    it('works without comment', function () {
        $revision = Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Proposed content']);

        $this->service->accept($revision);

        $revision->refresh();
        expect($revision->state)->toBe(RevisionStatus::Accepted);
        expect($revision->comment)->toBeNull();
    });
});

describe('reject', function () {
    it('sets state to rejected without updating target content', function () {
        $revision = Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create(['content' => 'Proposed content']);

        $this->service->reject($revision, 'Not relevant');

        $revision->refresh();
        expect($revision->state)->toBe(RevisionStatus::Rejected);
        expect($revision->comment)->toBe('Not relevant');

        $this->course->refresh();
        expect($this->course->content)->toBe('Original content');
    });

    it('dispatches RejectedRevisionEvent', function () {
        Event::fake([RejectedRevisionEvent::class]);

        $revision = Revision::factory()
            ->for($this->user)
            ->for($this->course, 'revisionable')
            ->create();

        $this->service->reject($revision);

        Event::assertDispatched(RejectedRevisionEvent::class, fn ($e) => $e->revision->id === $revision->id);
    });
});
