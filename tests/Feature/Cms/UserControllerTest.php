<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

describe('index', function () {
    it('paginates users', function () {
        User::factory()->count(20)->create();

        $this->actingAs($this->admin)
            ->get(route('cms.users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('users/index')
                ->has('pagination.data', 15)
                ->has('banned_filter')
            );
    });

    it('shows charts on first page without filters', function () {
        User::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('cms.users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('users/index')
                ->has('months')
                ->has('days')
            );
    });

    it('does not show charts with banned filter', function () {
        $this->actingAs($this->admin)
            ->get(route('cms.users.index', ['banned' => 1]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('users/index')
                ->where('banned_filter', true)
                ->missing('months')
                ->missing('days')
            );
    });

    it('filters banned users', function () {
        User::factory()->count(3)->create();
        User::factory()->count(2)->banned()->create();

        $this->actingAs($this->admin)
            ->get(route('cms.users.index', ['banned' => 1]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('users/index')
                ->has('pagination.data', 2)
                ->where('banned_filter', true)
            );
    });
});

describe('destroy (ban)', function () {
    it('bans a regular user', function () {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('cms.users.destroy', $user))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    });

    it('cannot ban a premium user', function () {
        $user = User::factory()->premium()->create();

        $this->actingAs($this->admin)
            ->delete(route('cms.users.destroy', $user))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    });
});
