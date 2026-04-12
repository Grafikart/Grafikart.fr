<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Technology;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->admin()->create();
    $this->validData = [
        'name' => 'React',
        'slug' => 'react',
        'content' => 'A JavaScript library for building user interfaces',
        'type' => 'library',
    ];
    $this->expectedRow = [
        'name' => 'React',
        'slug' => 'react',
        'content' => 'A JavaScript library for building user interfaces',
        'type' => 'library',
    ];
});

dataset('invalid_data', [
    'name empty' => ['name', ''],
    'name too short' => ['name', 'a'],
    'slug empty' => ['slug', ''],
    'slug too short' => ['slug', 'a'],
]);

describe('index', function () {
    it('paginates technologies', function () {
        Technology::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.technologies.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('technologies/index')
            );
    });

    it('returns JSON for autocomplete search', function () {
        Technology::factory()->create(['name' => 'React Native']);
        Technology::factory()->create(['name' => 'Vue.js']);
        Technology::factory()->create(['name' => 'Angular']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.technologies.index', ['q' => 'react']))
            ->assertOk()
            ->assertJson([
                ['name' => 'React Native'],
            ]);

        expect($response->json())->toHaveCount(1);
    });

    it('limits autocomplete results to 10', function () {
        Technology::factory()->count(15)->create(['name' => 'Test Tech']);

        $response = $this->actingAs($this->user)
            ->get(route('cms.technologies.index', ['q' => 'test']))
            ->assertOk();

        expect($response->json())->toHaveCount(10);
    });
});

describe('create', function () {
    it('displays the create form', function () {
        $this->actingAs($this->user)
            ->get(route('cms.technologies.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('technologies/form')
            );
    });
});

describe('store', function () {
    it('creates a new technology', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.technologies.store'), $this->validData)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('technologies', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('creates a technology with requirements', function () {
        $javascript = Technology::factory()->create(['name' => 'JavaScript']);
        $html = Technology::factory()->create(['name' => 'HTML']);

        $this->actingAs($this->user)
            ->post(route('cms.technologies.store'), [
                ...$this->validData,
                'requirements' => [$javascript->id, $html->id],
            ]);

        $technology = Technology::where('slug', 'react')->first();

        expect($technology->requirements)->toHaveCount(2);
        expect($technology->requirements->pluck('id')->toArray())->toContain($javascript->id, $html->id);
    });

    it('creates a technology with an image', function () {
        $file = UploadedFile::fake()->image('react.png');

        $this->actingAs($this->user)
            ->post(route('cms.technologies.store'), [
                ...$this->validData,
                'imageFile' => $file,
            ]);

        $technology = Technology::where('slug', 'react')->first();

        expect($technology->image)->not->toBeNull();
        Storage::disk('public')->assertExists('icons/'.$technology->image);
    });

    it('validates required fields', function (string $field, mixed $value) {
        $this->actingAs($this->user)
            ->post(route('cms.technologies.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('edit', function () {
    it('displays the edit form', function () {
        $technology = Technology::factory()->create();

        $this->actingAs($this->user)
            ->get(route('cms.technologies.edit', $technology))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('technologies/form')
                ->has('item')
            );
    });

    it('loads technology with requirements', function () {
        $technology = Technology::factory()->create();
        $requirement = Technology::factory()->create(['name' => 'JavaScript']);
        $technology->requirements()->attach($requirement);

        $this->actingAs($this->user)
            ->get(route('cms.technologies.edit', $technology))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('technologies/form')
                ->where('item.requirements.0.id', $requirement->id)
            );
    });
});

describe('update', function () {
    it('updates an existing technology', function () {
        Event::fake();
        $technology = Technology::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.technologies.update', $technology), $this->validData)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('technologies', [
            'id' => $technology->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('updates technology requirements', function () {
        $technology = Technology::factory()->create();
        $oldReq = Technology::factory()->create(['name' => 'Old']);
        $newReq = Technology::factory()->create(['name' => 'New']);

        $technology->requirements()->attach($oldReq);

        $this->actingAs($this->user)
            ->put(route('cms.technologies.update', $technology), [
                ...$this->validData,
                'requirements' => [$newReq->id],
            ]);

        $technology->refresh();

        expect($technology->requirements)->toHaveCount(1);
        expect($technology->requirements->first()->id)->toBe($newReq->id);
    });

    it('validates required fields on update', function (string $field, mixed $value) {
        $technology = Technology::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.technologies.update', $technology), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('destroy', function () {
    it('deletes a technology', function () {
        Event::fake();
        $technology = Technology::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.technologies.destroy', $technology))
            ->assertRedirect(route('cms.technologies.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('technologies', ['id' => $technology->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });

    it('deletes technology and its relationships', function () {
        $technology = Technology::factory()->create();
        $requirement = Technology::factory()->create();
        $technology->requirements()->attach($requirement);

        $this->actingAs($this->user)
            ->delete(route('cms.technologies.destroy', $technology))
            ->assertRedirect(route('cms.technologies.index'));

        $this->assertDatabaseMissing('technologies', ['id' => $technology->id]);
        $this->assertDatabaseMissing('technology_requirement', ['technology_id' => $technology->id]);
    });

});
