<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Path;
use App\Domains\Course\PathNode;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'title' => 'Test Path Title',
        'slug' => 'test-path-title',
        'createdAt' => '2024-01-01T10:00:00+01:00',
        'online' => true,
        'description' => 'This is the description of the test path.',
        'tags' => 'php, laravel, api',
        'nodes' => [],
    ];
    $this->expectedRow = [
        'title' => 'Test Path Title',
        'slug' => 'test-path-title',
        'created_at' => '2024-01-01 10:00:00',
        'online' => true,
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

    it('loads publication fields in the table rows', function () {
        Path::factory()->create([
            'online' => true,
            'created_at' => new DateTimeImmutable('2024-01-01 10:00:00'),
        ]);

        $this->actingAs($this->user)
            ->get(route('cms.paths.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('pagination.data.0.online', true)
                ->where('pagination.data.0.createdAt', fn ($value) => is_string($value) && $value !== '')
            );
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
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('paths', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('normalizes path tags', function () {
        $this->actingAs($this->user)
            ->post(route('cms.paths.store'), [
                ...$this->validData,
                'tags' => 'php, laravel, php, api ',
            ]);

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

    it('loads publication fields in edit form', function () {
        $path = Path::factory()->create([
            'online' => true,
            'created_at' => new DateTimeImmutable('2024-01-01 10:00:00'),
        ]);

        $this->actingAs($this->user)
            ->get(route('cms.paths.edit', $path))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('item.online', true)
                ->where('item.createdAt', fn ($value) => is_string($value) && $value !== '')
            );
    });

    it('loads fork meta in edit form', function () {
        $path = Path::factory()->create();
        $fork = PathNode::factory()->create([
            'path_id' => $path->id,
            'content_type' => 'fork',
            'meta' => ['video' => 'abc123'],
        ]);

        $this->actingAs($this->user)
            ->get(route('cms.paths.edit', $path))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('item.nodes.0.id', $fork->id)
                ->where('item.nodes.0.meta.video', 'abc123')
            );
    });
});

describe('update', function () {
    it('updates an existing path', function () {
        Event::fake();
        $path = Path::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.paths.update', $path), $this->validData)
            ->assertRedirect()
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
            ]);

        $path->refresh();
        expect($path->tags)->toBe('laravel, api');
    });

    it('updates fork meta video', function () {
        $path = Path::factory()->create();
        $fork = PathNode::factory()->create([
            'path_id' => $path->id,
            'content_type' => 'fork',
            'meta' => null,
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.paths.update', $path), [
                ...$this->validData,
                'nodes' => [
                    [
                        'id' => $fork->id,
                        'icon' => $fork->icon,
                        'title' => $fork->title,
                        'description' => $fork->description,
                        'contentType' => 'fork',
                        'contentId' => null,
                        'meta' => [
                            'video' => 'abc123',
                        ],
                        'x' => $fork->x,
                        'y' => $fork->y,
                        'parents' => [],
                    ],
                ],
            ]);

        $fork->refresh();
        expect($fork->meta)->toBe(['video' => 'abc123']);
    });

    it('clears fork meta when the video is empty', function () {
        $path = Path::factory()->create();
        $fork = PathNode::factory()->create([
            'path_id' => $path->id,
            'content_type' => 'fork',
            'meta' => ['video' => 'abc123'],
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.paths.update', $path), [
                ...$this->validData,
                'nodes' => [
                    [
                        'id' => $fork->id,
                        'icon' => $fork->icon,
                        'title' => $fork->title,
                        'description' => $fork->description,
                        'contentType' => 'fork',
                        'contentId' => null,
                        'meta' => [
                            'video' => '',
                        ],
                        'x' => $fork->x,
                        'y' => $fork->y,
                        'parents' => [],
                    ],
                ],
            ]);

        $fork->refresh();
        expect($fork->meta)->toBeNull();
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
