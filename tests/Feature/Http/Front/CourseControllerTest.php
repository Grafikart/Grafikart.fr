<?php

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;

describe('index', function () {
    it('displays courses index page', function () {
        Course::factory()->online()->count(3)->create();

        $this->get('/tutoriels')
            ->assertSuccessful();
    });

    it('filters by technology', function () {
        $tech = Technology::factory()->create(['slug' => 'laravel']);
        $matching = Course::factory()->online()->create();
        $matching->technologies()->attach($tech, ['version' => null, 'primary' => true]);
        Course::factory()->online()->create();

        $this->get('/tutoriels?technology=laravel')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 1);
    });

    it('filters by level', function () {
        Course::factory()->online()->junior()->count(2)->create();
        Course::factory()->online()->senior()->create();

        $this->get('/tutoriels?level=0')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 2);
    });

    it('filters by duration', function () {
        Course::factory()->online()->create(['duration' => 300]);
        Course::factory()->online()->create(['duration' => 500]);
        Course::factory()->online()->create(['duration' => 900]);

        $this->get('/tutoriels?duration=10')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 2);
    });

    it('filters by premium', function () {
        Course::factory()->online()->premium()->count(2)->create();
        Course::factory()->online()->free()->create();

        $this->get('/tutoriels?premium=1')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 2);
    });

    it('shows formations on /formations', function () {
        Formation::factory()->online()->count(2)->create();
        Course::factory()->online()->create();

        $this->get('/formations')
            ->assertSuccessful()
            ->assertViewHas('type', 'formation')
            ->assertViewHas('items', fn ($items) => $items->count() === 2);
    });

    it('filters formations by technology', function () {
        $tech = Technology::factory()->create(['slug' => 'react']);
        $matching = Formation::factory()->online()->create();
        $matching->technologies()->attach($tech, ['version' => null, 'primary' => true]);
        Formation::factory()->online()->create();

        $this->get('/formations?technology=react')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 1);
    });

    it('preserves query params in pagination links', function () {
        Course::factory()->online()->junior()->count(30)->create();

        $this->get('/tutoriels?level=0')
            ->assertSuccessful()
            ->assertSee('level=0');
    });

    it('combines multiple filters', function () {
        $tech = Technology::factory()->create(['slug' => 'php']);
        $matching = Course::factory()->online()->junior()->premium()->create();
        $matching->technologies()->attach($tech, ['version' => null, 'primary' => true]);

        Course::factory()->online()->senior()->premium()->create();
        Course::factory()->online()->junior()->free()->create();

        $this->get('/tutoriels?level=0&premium=1&technology=php')
            ->assertSuccessful()
            ->assertViewHas('items', fn ($items) => $items->count() === 1);
    });
});

describe('show', function () {
    it('redirects to replacement course when force_redirect is true', function () {
        $replacement = Course::factory()->online()->create();
        $deprecated = Course::factory()->create([
            'deprecated_by_id' => $replacement->id,
            'force_redirect' => true,
        ]);

        $this->get("/tutoriels/{$deprecated->slug}-{$deprecated->id}")
            ->assertRedirect(route('courses.show', ['slug' => $replacement->slug, 'course' => $replacement->id]))
            ->assertStatus(301);
    });

    it('does not redirect when force_redirect is false', function () {
        $replacement = Course::factory()->online()->create();
        $deprecated = Course::factory()->online()->create([
            'deprecated_by_id' => $replacement->id,
            'force_redirect' => false,
        ]);

        $this->get("/tutoriels/{$deprecated->slug}-{$deprecated->id}")
            ->assertSuccessful();
    });

    it('does not redirect when no replacement course is set', function () {
        $course = Course::factory()->online()->create(['force_redirect' => true]);

        $this->get("/tutoriels/{$course->slug}-{$course->id}")
            ->assertSuccessful();
    });
});
