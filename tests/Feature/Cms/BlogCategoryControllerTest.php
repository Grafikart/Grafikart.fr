<?php

use App\Domains\Blog\BlogCategory;
use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'name' => 'Category Name',
        'slug' => 'category-name',
    ];
    $this->expectedRow = [
        'name' => 'Category Name',
        'slug' => 'category-name',
    ];
});

dataset('invalid_data', [
    'name empty' => ['name', ''],
    'name too short' => ['name', 'a'],
    'slug too short' => ['slug', 'a'],
]);

describe('index', function () {
    it('paginates blog categories', function () {
        BlogCategory::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.blog_categories.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/categories/index')
            );
    });
});

describe('store', function () {
    it('creates a new blog category', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.blog_categories.store'), $this->validData)
            ->assertRedirect(route('cms.blog_categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blog_categories', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('validates required fields', function (string $field, mixed $value) {
        $this->actingAs($this->user)
            ->post(route('cms.blog_categories.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('update', function () {
    it('updates an existing blog category', function () {
        Event::fake();
        $blogCategory = BlogCategory::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.blog_categories.update', $blogCategory), $this->validData)
            ->assertRedirect(route('cms.blog_categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blog_categories', [
            'id' => $blogCategory->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('validates required fields on update', function (string $field, mixed $value) {
        $blogCategory = BlogCategory::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.blog_categories.update', $blogCategory), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');

    it('validates slug uniqueness on update', function () {
        $existingCategory = BlogCategory::factory()->create(['slug' => 'existing-slug']);
        $blogCategory = BlogCategory::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.blog_categories.update', $blogCategory), [...$this->validData, 'slug' => 'existing-slug'])
            ->assertInvalid(['slug']);
    });
});

describe('destroy', function () {
    it('deletes a blog category', function () {
        Event::fake();
        $blogCategory = BlogCategory::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.blog_categories.destroy', $blogCategory))
            ->assertRedirect(route('cms.blog_categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('blog_categories', ['id' => $blogCategory->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
