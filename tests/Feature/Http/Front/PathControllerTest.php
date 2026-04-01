<?php

use App\Domains\Course\Path;

describe('index', function () {
    it('shows only published paths', function () {
        Path::factory()->online()->create([
            'slug' => 'published-path',
            'created_at' => now()->subDay(),
        ]);
        Path::factory()->offline()->create([
            'slug' => 'offline-path',
            'created_at' => now()->subDay(),
        ]);
        Path::factory()->online()->create([
            'slug' => 'future-path',
            'created_at' => now()->addDay(),
        ]);

        $this->get(route('paths.index'))
            ->assertSuccessful()
            ->assertViewHas('paths', fn ($paths) => $paths->count() === 1)
            ->assertSee('published-path')
            ->assertDontSee('offline-path')
            ->assertDontSee('future-path');
    });
});

describe('show', function () {
    it('shows a published path', function () {
        $path = Path::factory()->online()->create([
            'slug' => 'published-path',
            'created_at' => now()->subDay(),
        ]);

        $this->get(route('paths.show', ['slug' => $path->slug, 'path' => $path]))
            ->assertSuccessful()
            ->assertViewHas('path', fn ($item) => $item->id === $path->id);
    });
});
