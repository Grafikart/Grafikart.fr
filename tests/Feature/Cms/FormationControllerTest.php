<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'title' => 'Test Formation Title',
        'slug' => 'test-formation-title',
        'content' => 'This is the content of the test formation.',
        'online' => true,
        'forceRedirect' => false,
        'level' => 0,
        'short' => 'A short description',
        'youtubePlaylist' => null,
        'links' => null,
        'deprecatedBy' => null,
        'image' => null,
        'createdAt' => '2024-01-01T10:00:00+01:00',
        'chapters' => [],
    ];
    $this->expectedRow = [
        'title' => 'Test Formation Title',
        'slug' => 'test-formation-title',
        'content' => 'This is the content of the test formation.',
        'online' => true,
        'force_redirect' => false,
        'level' => 0,
        'short' => 'A short description',
        'created_at' => '2024-01-01 10:00:00',
    ];
});

dataset('invalid_data', [
    'title empty' => ['title', ''],
    'title too short' => ['title', 'a'],
    'slug empty' => ['slug', ''],
    'slug too short' => ['slug', 'a'],
    'content empty' => ['content', ''],
    'invalid deprecated by' => ['deprecatedBy', 9999],
    'malformed chapters' => ['chapters', [['a' => 3]], 'chapters.0.title'],
    'invalid level' => ['level', 10],
]);

describe('index', function () {
    it('paginates formations', function () {
        Formation::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.formations.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('formations/index')
            );
    });

    it('searches formations by title and content', function () {
        Formation::factory()->create(['title' => 'React Tutorial']);
        Formation::factory()->create(['content' => 'Learn React here']);
        Formation::factory()->create(['title' => 'Vue Tutorial']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.formations.index', ['q' => 'react']))
            ->assertOk();

        expect($response->viewData('page')['props']['pagination']['total'])->toBe(2);
    });
});

describe('store', function () {
    it('creates a new formation', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.formations.store'), $this->validData)
            ->assertRedirect(route('cms.formations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('formations', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('creates a formation with chapters', function () {
        $chapter1 = \App\Domains\Course\Course::factory(3)->create();
        $chapter2 = \App\Domains\Course\Course::factory(2)->create();
        $this->actingAs($this->user)
            ->post(route('cms.formations.store'), [
                ...$this->validData,
                'chapters' => [
                    ['title' => 'Introduction', 'ids' => $chapter1->pluck('id')->toArray()],
                    ['title' => 'Advanced Topics', 'ids' => $chapter2->pluck('id')->toArray()],
                ],
            ])
            ->assertRedirect(route('cms.formations.index'));

        $formation = Formation::where('slug', $this->validData['slug'])->first();
        expect($formation->chapters)->toHaveCount(2);
        expect($formation->chapters[0]->title)->toBe('Introduction');
        expect($formation->chapters[0]->ids)->toBe([1, 2, 3]);
        expect($formation->chapters[1]->title)->toBe('Advanced Topics');
        expect($formation->chapters[1]->ids)->toBe([4, 5]);
        expect($formation->courses()->count())->toBe(5);
    });

    it('validates required fields', function (string $field, mixed $value, ?string $validationKey = null) {
        $this->actingAs($this->user)
            ->post(route('cms.formations.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$validationKey ?? $field]);
    })->with('invalid_data');
});

describe('update', function () {
    it('updates an existing formation', function () {
        Event::fake();
        $formation = Formation::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.formations.update', $formation), $this->validData)
            ->assertRedirect(route('cms.formations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('removes formation_id from courses removed from chapters', function () {
        Event::fake();
        $this->user = User::factory()->admin()->create();
        $courses = \App\Domains\Course\Course::factory(3)->create();
        $formation = Formation::factory()->create([
            'chapters' => [
                new \App\Domains\Course\Chapter(title: 'Chapter 1', ids: $courses->pluck('id')->toArray()),
            ],
        ]);
        // Set formation_id on all 3 courses
        $courses->each->update(['formation_id' => $formation->id]);

        // Update formation keeping only the first course
        $this->actingAs($this->user)
            ->put(route('cms.formations.update', $formation), [
                ...$this->validData,
                'chapters' => [
                    ['title' => 'Chapter 1', 'ids' => [$courses[0]->id]],
                ],
            ])
            ->assertRedirect(route('cms.formations.index'));

        // Course 1 should still belong to the formation
        expect($courses[0]->fresh()->formation_id)->toBe($formation->id);
        // Courses 2 and 3 should have formation_id set to null
        expect($courses[1]->fresh()->formation_id)->toBeNull();
        expect($courses[2]->fresh()->formation_id)->toBeNull();
    });

    it('validates required fields on update', function (string $field, mixed $value, ?string $validationKey = null) {
        $formation = Formation::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.formations.update', $formation), [...$this->validData, $field => $value])
            ->assertInvalid([$validationKey ?? $field]);
    })->with('invalid_data');
});

describe('destroy', function () {
    it('deletes a formation', function () {
        Event::fake();
        $formation = Formation::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.formations.destroy', $formation))
            ->assertRedirect(route('cms.formations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('formations', ['id' => $formation->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});

describe('technologies', function () {
    it('creates a formation with technologies', function () {
        $php = Technology::factory()->create(['name' => 'PHP']);
        $laravel = Technology::factory()->create(['name' => 'Laravel']);

        $this->actingAs($this->user)
            ->post(route('cms.formations.store'), [
                ...$this->validData,
                'technologies' => [
                    ['id' => $php->id, 'version' => '8.3', 'primary' => '1'],
                    ['id' => $laravel->id, 'version' => '12'],
                ],
            ])
            ->assertRedirect(route('cms.formations.index'));

        $formation = Formation::where('slug', 'test-formation-title')->first();
        expect($formation->technologies)->toHaveCount(2);
        expect($formation->technologies->firstWhere('id', $php->id)->pivot->version)->toBe('8.3');
        expect((bool) $formation->technologies->firstWhere('id', $php->id)->pivot->primary)->toBeTrue();
        expect((bool) $formation->technologies->firstWhere('id', $laravel->id)->pivot->primary)->toBeFalse();
    });

    it('updates formation technologies', function () {
        $formation = Formation::factory()->create();
        $tech = Technology::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.formations.update', $formation), [
                ...$this->validData,
                'technologies' => [
                    ['id' => $tech->id, 'version' => '1.0', 'primary' => '1'],
                ],
            ])
            ->assertRedirect(route('cms.formations.index'));

        $formation->refresh();
        expect($formation->technologies)->toHaveCount(1);
    });

    it('loads technologies in edit form', function () {
        $formation = Formation::factory()->withTechnologies(2)->create();

        $this->actingAs($this->user)
            ->get(route('cms.formations.edit', $formation))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('item.technologies', 2)
            );
    });
});
