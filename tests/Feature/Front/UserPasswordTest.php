<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('updates the password in the database', function () {
    $this->actingAs($this->user)
        ->post(route('users.password'), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password', $this->user->fresh()->password))->toBeTrue();
});

it('does not update the password when confirmation does not match', function () {
    $originalPassword = $this->user->password;

    $this->actingAs($this->user)
        ->post(route('users.password'), [
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('password');

    expect($this->user->fresh()->password)->toBe($originalPassword);
});
