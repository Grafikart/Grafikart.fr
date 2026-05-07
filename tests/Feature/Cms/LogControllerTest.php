<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
});

it('shows the log page', function () {
    $this->actingAs($this->user)
        ->get(route('cms.logs.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('logs/index')->has('output'));
});

it('requires authentication', function () {
    $this->get(route('cms.logs.index'))
        ->assertRedirect(route('login'));
});
