<?php

use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Support\ContactRequest;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->contactRequest = ContactRequest::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'Bonjour, j\'ai une question.',
        'ip' => '127.0.0.1',
    ]);
});

describe('index', function () {
    it('paginates contact requests', function () {
        ContactRequest::factory()->count(20)->create();

        $this->actingAs($this->user)
            ->get(route('cms.contact_requests.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('contact-requests/index')
                ->where('pagination.data.0.id', ContactRequest::query()->latest('id')->first()->id)
            );
    });

    it('denies access to non-admin users', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('cms.contact_requests.index'))
            ->assertForbidden();
    });
});

describe('show', function () {
    it('displays a contact request', function () {
        $this->actingAs($this->user)
            ->get(route('cms.contact_requests.show', $this->contactRequest))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('contact-requests/show')
                ->where('item.id', $this->contactRequest->id)
                ->where('item.name', 'John Doe')
                ->where('item.email', 'john@example.com')
            );
    });

    it('denies access to non-admin users', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('cms.contact_requests.show', $this->contactRequest))
            ->assertForbidden();
    });
});

describe('destroy', function () {
    it('deletes a contact request', function () {
        Event::fake();

        $this->actingAs($this->user)
            ->delete(route('cms.contact_requests.destroy', $this->contactRequest))
            ->assertRedirect(route('cms.contact_requests.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('contact_requests', [
            'id' => $this->contactRequest->id,
        ]);

        Event::assertDispatched(ContentDeletedEvent::class);
    });

    it('denies access to non-admin users', function () {
        $this->actingAs(User::factory()->create())
            ->delete(route('cms.contact_requests.destroy', $this->contactRequest))
            ->assertForbidden();
    });
});
