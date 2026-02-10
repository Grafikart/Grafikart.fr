<?php

use App\Domains\Course\Course;
use App\Domains\Course\Factory\TechnologyFactory;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;

beforeEach(function () {
    TechnologyFactory::resetSequence();
});

describe('index', function () {
    it('returns filter data', function () {
        $tech = Technology::factory()->create();
        Course::factory()->online()->create()
            ->technologies()->attach($tech, ['version' => null, 'primary' => true]);

        $this->getJson('/api/courses/filters')
            ->assertSuccessful()
            ->assertJsonStructure([
                'technologies' => [['label', 'value', 'courses_count', 'formations_count']],
                'levels' => [['label', 'value']],
                'types' => ['course', 'formation'],
            ]);
    });

    it('counts technologies from published courses only', function () {
        $tech = Technology::factory()->create();

        Course::factory()->online()->count(3)->create()
            ->each(fn ($c) => $c->technologies()->attach($tech, ['version' => null, 'primary' => true]));
        Course::factory()->offline()->create()
            ->technologies()->attach($tech, ['version' => null, 'primary' => true]);

        $this->getJson('/api/courses/filters')
            ->assertSuccessful()
            ->assertJsonPath('technologies.0.courses_count', 3);
    });

    it('returns type counts', function () {
        $formation = Formation::factory()->online()->create();
        Course::factory()->online()->count(3)->create(['formation_id' => null]);
        Course::factory()->online()->create(['formation_id' => $formation->id]);

        $this->getJson('/api/courses/filters')
            ->assertSuccessful()
            ->assertJsonPath('types.course', 3)
            ->assertJsonPath('types.formation', 1);
    });

    it('excludes technologies with zero published courses', function () {
        Technology::factory()->create(['name' => 'Unused', 'slug' => 'unused']);

        $response = $this->getJson('/api/courses/filters')->assertSuccessful();
        $slugs = collect($response->json('technologies'))->pluck('value');

        expect($slugs)->not->toContain('unused');
    });
});
