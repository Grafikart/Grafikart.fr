<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function fakeSocialiteUser(array $attributes = []): SocialiteUser
{
    return (new SocialiteUser)->map([
        'id' => '12345',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        ...$attributes,
    ]);
}

describe('checkPremium', function () {
    it('redirects guests to login', function () {
        $this->get('/auth/check/premium')->assertRedirect(route('login'));
    });

    it('returns 403 for non-premium users', function () {
        $this->actingAs(User::factory()->create())
            ->get('/auth/check/premium')
            ->assertForbidden();
    });

    it('returns 204 for premium users', function () {
        $this->actingAs(User::factory()->premium()->create())
            ->get('/auth/check/premium')
            ->assertNoContent();
    });
});

describe('oauth connect', function () {
    it('redirects to the oauth provider', function () {
        $this->get('/oauth/connect/github')
            ->assertRedirect();
    });
});

describe('oauth callback', function () {
    it('logs in an existing user matched by oauth id', function () {
        $user = User::factory()->create(['github_id' => '12345']);

        Socialite::fake('github', fakeSocialiteUser());

        $this->get('/oauth/check/github')
            ->assertRedirect(route('users.edit'));

        $this->assertAuthenticatedAs($user);
    });

    it('links oauth id and logs in when user is matched by email', function () {
        $user = User::factory()->create(['email' => 'john@example.com']);

        Socialite::fake('github', fakeSocialiteUser());

        $this->get('/oauth/check/github')
            ->assertRedirect(route('users.edit'));

        $this->assertAuthenticatedAs($user);
        expect($user->fresh()->github_id)->toBe('12345');
    });

    it('creates a new user when no match is found', function () {
        Socialite::fake('github', fakeSocialiteUser());

        $this->get('/oauth/check/github')
            ->assertRedirect(route('users.edit'));

        $user = User::where('email', 'john@example.com')->first();
        expect($user)->not->toBeNull();
        expect($user->name)->toBe('John Doe');
        expect($user->github_id)->toBe('12345');
        $this->assertAuthenticatedAs($user);
    });

    it('appends a suffix to the name when it already exists', function () {
        User::factory()->create(['name' => 'John Doe']);

        Socialite::fake('github', fakeSocialiteUser());

        $this->get('/oauth/check/github')
            ->assertRedirect(route('users.edit'));

        $user = User::where('email', 'john@example.com')->first();
        expect($user->name)->toStartWith('John Doe_');
        expect($user->name)->not->toBe('John Doe');
    });

    it('redirects with error when oauth id is null', function () {
        Socialite::fake('github', fakeSocialiteUser(['id' => null]));

        $this->get('/oauth/check/github')
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    });

    it('redirects with error when no email and no existing user', function () {
        Socialite::fake('github', fakeSocialiteUser(['email' => null]));

        $this->get('/oauth/check/github')
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    });

    it('redirects with error when no name and no existing user by email', function () {
        Socialite::fake('github', fakeSocialiteUser(['name' => null]));

        $this->get('/oauth/check/github')
            ->assertRedirect(route('home'))
            ->assertSessionHas('error');
    });
});
