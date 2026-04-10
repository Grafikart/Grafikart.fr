<?php

use App\Domains\Badge\Badge;
use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'name' => 'Badge de test',
        'description' => 'Un badge de démonstration',
        'position' => 10,
        'action' => 'comment',
        'actionCount' => 5,
        'theme' => 'blue',
        'image' => 'https://example.com/badge.png',
        'unlockable' => true,
    ];
});

describe('index', function () {
    it('paginates badges', function () {
        Badge::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.badges.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('badges/index')
            );
    });

});

describe('create', function () {
    it('displays the create form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.badges.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('badges/form')
            );
    });
});

describe('store', function () {
    it('creates a badge', function () {
        Event::fake([ContentCreatedEvent::class]);

        $this->actingAs($this->user)
            ->post(route('cms.badges.store'), $this->validData)
            ->assertRedirect(route('cms.badges.index'))
            ->assertSessionHas('success');

        $badge = Badge::query()->where('name', 'Badge de test')->first();

        expect($badge)->not->toBeNull();

        Event::assertDispatched(ContentCreatedEvent::class);
    });
});

describe('edit', function () {
    it('displays the edit form', function () {
        $badge = Badge::factory()->create();

        $this->actingAs($this->user)
            ->get(route('cms.badges.edit', $badge))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('badges/form')
            );
    });
});

describe('update', function () {
    it('updates a badge', function () {
        Event::fake([ContentUpdatedEvent::class]);
        $badge = Badge::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.badges.update', $badge), $this->validData)
            ->assertRedirect(route('cms.badges.index'))
            ->assertSessionHas('success');

        $badge->refresh();

        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'name' => 'Badge de test',
            'action_count' => 5,
            'theme' => 'blue',
            'unlockable' => true,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });
});

describe('destroy', function () {
    it('deletes a badge', function () {
        Event::fake([ContentDeletedEvent::class]);
        $badge = Badge::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.badges.destroy', $badge))
            ->assertRedirect(route('cms.badges.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
