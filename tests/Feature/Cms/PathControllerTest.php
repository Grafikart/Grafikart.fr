<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Path;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'title' => 'Test Path Title',
        'slug' => 'test-path-title',
        'description' => 'This is the description of the test path.',
        'tags' => 'php, laravel, api',
        'nodes' => [],
    ];
    $this->expectedRow = [
        'title' => 'Test Path Title',
        'slug' => 'test-path-title',
        'description' => 'This is the description of the test path.',
        'tags' => 'php, laravel, api',
    ];
});

describe('index', function () {
    it('paginates paths', function () {
        Path::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.paths.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('paths/index')
            );
    });

    it('loads tags in the table rows', function () {
        Path::factory()->create(['tags' => 'php, laravel']);

        $this->actingAs($this->user)
            ->get(route('cms.paths.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('pagination.data.0.tags', 'php, laravel')
            );
    });
});

describe('search content', function () {
    it('returns courses matching the selected type', function () {
        $matchingCourse = Course::factory()->create([
            'title' => 'Laravel avancé',
            'content' => 'cours complet',
        ]);
        Formation::factory()->create([
            'title' => 'Laravel avancé',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('cms.paths.search-content', [
                'q' => 'Laravel',
                'type' => 'course',
            ]))
            ->assertOk();

        expect($response->json())->toBe([
            [
                'id' => $matchingCourse->id,
                'name' => 'Laravel avancé',
            ],
        ]);
    });

    it('limits search results to 10 items', function () {
        Course::factory()->count(12)->sequence(
            fn (\Illuminate\Database\Eloquent\Factories\Sequence $sequence): array => [
                'title' => "React {$sequence->index}",
                'content' => 'react',
            ],
        )->create();

        $response = $this->actingAs($this->user)
            ->get(route('cms.paths.search-content', [
                'q' => 'React',
                'type' => 'course',
            ]))
            ->assertOk();

        expect($response->json())->toHaveCount(10);
    });
});

describe('create', function () {
    it('displays the create form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.paths.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('paths/form')
            );
    });
});

describe('store', function () {
    it('creates a new path', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.paths.store'), $this->validData)
            ->assertRedirect(route('cms.paths.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('paths', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('normalizes path tags', function () {
        $this->actingAs($this->user)
            ->post(route('cms.paths.store'), [
                ...$this->validData,
                'tags' => 'php, laravel, php, api ',
            ])
            ->assertRedirect(route('cms.paths.index'));

        $path = Path::where('slug', 'test-path-title')->first();
        expect($path->tags)->toBe('php, laravel, api');
    });
});

describe('edit', function () {
    it('displays the edit form', function () {
        $path = Path::factory()->create();

        $this->actingAs($this->user)
            ->get(route('cms.paths.edit', $path))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('paths/form')
                ->has('item')
            );
    });

    it('loads tags in edit form', function () {
        $path = Path::factory()->create(['tags' => 'php, laravel']);

        $this->actingAs($this->user)
            ->get(route('cms.paths.edit', $path))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('item.tags', 'php, laravel')
            );
    });
});

describe('update', function () {
    it('updates an existing path', function () {
        Event::fake();
        $path = Path::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.paths.update', $path), $this->validData)
            ->assertRedirect(route('cms.paths.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('paths', [
            'id' => $path->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('updates path tags', function () {
        $path = Path::factory()->create(['tags' => 'php']);

        $this->actingAs($this->user)
            ->put(route('cms.paths.update', $path), [
                ...$this->validData,
                'tags' => 'laravel, api',
            ])
            ->assertRedirect(route('cms.paths.index'));

        $path->refresh();
        expect($path->tags)->toBe('laravel, api');
    });
});

describe('destroy', function () {
    it('deletes a path', function () {
        Event::fake();
        $path = Path::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.paths.destroy', $path))
            ->assertRedirect(route('cms.paths.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('paths', ['id' => $path->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
