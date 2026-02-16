<?php

use App\Infrastructure\Mailing\Mail\UserDeletedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->user = User::factory()->create();
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
    Mail::fake();

    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'password',
            'reason' => '',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    Mail::assertNotSent(UserDeletedMail::class);
    $this->assertSoftDeleted('users', ['id' => $this->user->id]);
});

it('sends an email when a reason is provided', function () {
    Mail::fake();

    $this->actingAs($this->user)
        ->delete(route('users.delete'), [
            'password' => 'password',
            'reason' => 'I no longer need this account',
        ])
        ->assertRedirect();

    Mail::assertSent(UserDeletedMail::class, function (UserDeletedMail $mail) {
        return $mail->user->is($this->user)
            && $mail->reason === 'I no longer need this account';
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
