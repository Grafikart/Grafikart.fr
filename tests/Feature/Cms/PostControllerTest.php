<?php

use App\Domains\Blog\BlogCategory;
use App\Domains\Blog\Post;
use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = new User;
    $this->category = BlogCategory::factory()->create();
    $this->validData = [
        'title' => 'Test Post Title',
        'slug' => 'test-post-title',
        'content' => 'This is the content of the test post.',
        'online' => true,
        'categoryId' => $this->category->id,
        'attachmentId' => null,
    ];
    $this->expectedRow = [
        'title' => 'Test Post Title',
        'slug' => 'test-post-title',
        'content' => 'This is the content of the test post.',
        'online' => true,
        'category_id' => $this->category->id,
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
    it('paginates posts', function () {
        Post::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.posts.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/index')
            );
    });

    it('filters posts by category', function () {
        $category1 = BlogCategory::factory()->create();
        $category2 = BlogCategory::factory()->create();

        Post::factory()->count(5)->create(['category_id' => $category1->id]);
        Post::factory()->count(3)->create(['category_id' => $category2->id]);

        $response = $this->actingAs($this->user)
            ->get(route('cms.posts.index', ['category' => $category1->id]))
            ->assertOk();

        expect($response->viewData('page')['props']['pagination']['total'])->toBe(5);
    });

    it('searches posts by title and content', function () {
        Post::factory()->create(['title' => 'React Tutorial']);
        Post::factory()->create(['content' => 'Learn React here']);
        Post::factory()->create(['title' => 'Vue Tutorial']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.posts.index', ['q' => 'react']))
            ->assertOk();

        expect($response->viewData('page')['props']['pagination']['total'])->toBe(2);
    });
});

describe('create', function () {
    it('displays the create form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.posts.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/form')
                ->has('categories')
            );
    });
});

describe('store', function () {
    it('creates a new post', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.posts.store'), $this->validData)
            ->assertRedirect(route('cms.posts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blog_posts', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('creates a post without category', function () {
        $this->actingAs($this->user)
            ->post(route('cms.posts.store'), [
                ...$this->validData,
                'categoryId' => null,
            ])
            ->assertRedirect(route('cms.posts.index'));

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Test Post Title',
            'category_id' => null,
        ]);
    });

    it('validates required fields', function (string $field, mixed $value) {
        $this->actingAs($this->user)
            ->post(route('cms.posts.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('edit', function () {
    it('displays the edit form', function () {
        $post = Post::factory()->create();

        $this->actingAs($this->user)
            ->get(route('cms.posts.edit', $post))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/form')
                ->has('item')
                ->has('categories')
            );
    });

    it('loads post with category and attachment', function () {
        $post = Post::factory()->create(['category_id' => $this->category->id]);

        $this->actingAs($this->user)
            ->get(route('cms.posts.edit', $post))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('blog/form')
                ->where('item.categoryId', $this->category->id)
            );
    });
});

describe('update', function () {
    it('updates an existing post', function () {
        Event::fake();
        $post = Post::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.posts.update', $post), $this->validData)
            ->assertRedirect(route('cms.posts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blog_posts', [
            'id' => $post->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('validates required fields on update', function (string $field, mixed $value) {
        $post = Post::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.posts.update', $post), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('destroy', function () {
    it('deletes a post', function () {
        Event::fake();
        $post = Post::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.posts.destroy', $post))
            ->assertRedirect(route('cms.posts.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('blog_posts', ['id' => $post->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
