<?php

use App\Domains\Blog\Post;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Comment\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->post = Post::factory()->create();
});

describe('index', function () {
    it('paginates comments', function () {
        Comment::factory()->count(20)->for($this->post, 'commentable')->create();

        $this->actingAs($this->user)
            ->get(route('cms.comments.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('comments/index')
            );
    });

    it('loads user and commentable relationships', function () {
        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->for($this->post, 'commentable')->create();

        $response = $this->actingAs($this->user)
            ->get(route('cms.comments.index'))
            ->assertOk();

        $comments = $response->viewData('page')['props']['pagination']['data'];
        expect($comments[0]['id'])->toBe($comment->id);
    });
});

describe('update', function () {
    it('updates an existing comment', function () {
        Event::fake();
        $comment = Comment::factory()->for($this->post, 'commentable')->create([
            'content' => 'Old content',
        ]);

        $this->actingAs($this->user)
            ->put(route('cms.comments.update', $comment), ['content' => 'New content'])
            ->assertRedirect(route('cms.comments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'New content',
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('validates content is required', function () {
        $comment = Comment::factory()->for($this->post, 'commentable')->create();

        $this->actingAs($this->user)
            ->put(route('cms.comments.update', $comment), ['content' => ''])
            ->assertInvalid(['content']);
    });
});

describe('destroy', function () {
    it('deletes a comment', function () {
        Event::fake();
        $comment = Comment::factory()->for($this->post, 'commentable')->create();

        $this->actingAs($this->user)
            ->delete(route('cms.comments.destroy', $comment))
            ->assertRedirect(route('cms.comments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
