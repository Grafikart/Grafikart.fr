<?php

use App\Infrastructure\Mailing\Notification\UserDeletionNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->admin()->create();
});

it('does not delete the user when the password is incorrect', function () {
    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'wrong-password',
            'reason' => '',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'deleted_at' => null,
    ]);
});

it('allows an empty reason', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'password',
            'reason' => '',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    Notification::assertNothingSent();
    $this->assertSoftDeleted('users', ['id' => $this->user->id]);
});

it('sends an email when a reason is provided', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'password',
            'reason' => 'I no longer need this account',
        ])
        ->assertRedirect();

    Notification::assertSentTo($this->admin, function (UserDeletionNotification $notification, array $channels) {
        return $channels === ['mail']
            && $notification->toMail($this->admin)->introLines[1] === 'I no longer need this account';
    });
});

it('soft deletes the user when the password is correct', function () {
    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'password',
            'reason' => '',
        ])
        ->assertRedirect();

    $this->assertSoftDeleted('users', ['id' => $this->user->id]);
    $this->assertGuest();
});
