<?php

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Premium\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = new User;
    $this->validData = [
        'name' => 'Plan Premium',
        'price' => 99,
        'duration' => 12,
        'stripeId' => 'price_1234567890',
    ];
    $this->expectedRow = [
        'name' => 'Plan Premium',
        'price' => 99,
        'duration' => 12,
        'stripe_id' => 'price_1234567890',
    ];
});

dataset('invalid_data', [
    'name empty' => ['name', ''],
    'name too short' => ['name', 'a'],
    'stripeId empty' => ['stripeId', ''],
    'stripeId too short' => ['stripeId', 'a'],
]);

describe('index', function () {
    it('paginates plans', function () {
        Plan::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.plans.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('plans/index')
                ->has('pagination.data', 15)
            );
    });
});

describe('store', function () {
    it('creates a new plan', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->post(route('cms.plans.store'), $this->validData)
            ->assertRedirect(route('cms.plans.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plans', $this->expectedRow);

        Event::assertDispatched(ContentCreatedEvent::class);
    });

    it('validates required fields', function (string $field, mixed $value) {
        $this->actingAs($this->user)
            ->post(route('cms.plans.store'), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('update', function () {
    it('updates an existing plan', function () {
        Event::fake();
        $plan = Plan::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.plans.update', $plan), $this->validData)
            ->assertRedirect(route('cms.plans.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            ...$this->expectedRow,
        ]);

        Event::assertDispatched(ContentUpdatedEvent::class);
    });

    it('validates required fields on update', function (string $field, mixed $value) {
        $plan = Plan::factory()->create();

        $this->actingAs($this->user)
            ->put(route('cms.plans.update', $plan), [...$this->validData, $field => $value])
            ->assertInvalid([$field]);
    })->with('invalid_data');
});

describe('destroy', function () {
    it('deletes a plan', function () {
        Event::fake();
        $plan = Plan::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('cms.plans.destroy', $plan))
            ->assertRedirect(route('cms.plans.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('plans', ['id' => $plan->id]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });
});
