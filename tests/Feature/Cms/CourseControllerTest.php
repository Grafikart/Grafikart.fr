<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = new User;
    $this->validData = [
        'title' => 'Test Course Title',
        'slug' => 'test-course-title',
        'content' => 'This is the content of the test course.',
        'online' => true,
        'premium' => false,
        'forceRedirect' => false,
        'level' => 0,
        'youtubeId' => null,
        'videoPath' => null,
        'demo' => null,
        'deprecatedBy' => null,
        'image' => null,
        'youtubeThumbnail' => null,
    ];
    $this->expectedRow = [
        'title' => 'Test Course Title',
        'slug' => 'test-course-title',
        'content' => 'This is the content of the test course.',
        'online' => true,
        'premium' => false,
        'force_redirect' => false,
        'level' => 0,
    ];
});

dataset('invalid_data', [
    'title empty' => ['title', ''],
    'title too short' => ['title', 'a'],
    'slug empty' => ['slug', ''],
    'slug too short' => ['slug', 'a'],
    'content empty' => ['content', ''],
]);

describe('index', function () {
    it('paginates courses', function () {
        Course::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.courses.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('courses/index')
            );
    });

    it('searches courses by title and content', function () {
        Course::factory()->create(['title' => 'React Tutorial']);
        Course::factory()->create(['content' => 'Learn React here']);
        Course::factory()->create(['title' => 'Vue Tutorial']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.courses.index', ['q' => 'react']))
            ->assertOk();

        expect($response->viewData('page')['props']['pagination']['total'])->toBe(2);
    });
});

describe('create', function () {
    it('displays the create form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.courses.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('courses/form')
            );
    });
});

describe('store', function () {
    it('creates a new course', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.courses.store'), $this->validData)
            ->assertRedirect(route('cms.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('courses', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('creates a premium course', function () {
        $this->actingAs($this->user)
            ->post(route('cms.courses.store'), [
                ...$this->validData,
                'premium' => true,
            ])
            ->assertRedirect(route('cms.courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course Title',
            'premium' => true,
        ]);
    });

    it('validates required fields', function (string $field, mixed $value) {
        $this->actingAs($this->user)
            ->post(route('cms.courses.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('edit', function () {
    it('displays the edit form', function () {
        $course = Course::factory()->create();

        $this->actingAs($this->user)
            ->get(route('cms.courses.edit', $course))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('courses/form')
                ->has('item')
            );
    });
});

describe('update', function () {
    it('updates an existing course', function () {
        Event::fake();
        $course = Course::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.courses.update', $course), $this->validData)
            ->assertRedirect(route('cms.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('validates required fields on update', function (string $field, mixed $value) {
        $course = Course::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.courses.update', $course), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('destroy', function () {
    it('deletes a course', function () {
        Event::fake();
        $course = Course::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.courses.destroy', $course))
            ->assertRedirect(route('cms.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
