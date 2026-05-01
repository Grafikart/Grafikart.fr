<?php

use App\Domains\Coupon\Coupon;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertDontSee('Code étudiant');
});

test('registration screen prefills coupon and email from valid coupon query', function () {
    $coupon = Coupon::factory()->create([
        'id' => 'GDUNIV_qCP0flx0',
        'email' => 'student@example.com',
    ]);

    $response = $this->get(route('register', ['coupon' => $coupon->id]));

    $response->assertOk();
    $response->assertSee('Code étudiant');
    $response->assertSee('value="'.$coupon->id.'"', false);
    $response->assertSee('value="'.$coupon->email.'"', false);
});

test('registration screen sends empty coupon data for invalid coupon query', function () {
    $response = $this->get(route('register', ['coupon' => 'UNKNOWN']));

    $response->assertOk();
    $response->assertDontSee('Code étudiant');
    $response->assertDontSee('value="UNKNOWN"', false);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('users.edit', absolute: false));
});

test('new users can register with a coupon', function () {
    $coupon = Coupon::factory()->create([
        'id' => 'GDUNIV_qCP0flx0',
        'email' => 'student@example.com',
        'months' => 3,
    ]);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'coupon' => $coupon->id,
    ]);

    $response->assertRedirect(route('users.edit', absolute: false));

    $coupon->refresh();
    expect($coupon->user_id)->not->toBeNull();
    expect($coupon->claimed_at)->not->toBeNull();
    expect($coupon->user->premium_end_at)->not->toBeNull();
    expect($coupon->user->premium_end_at->isFuture())->toBeTrue();
    expect($coupon->user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(3)->format('Y-m-d'));
});

test('invalid coupon blocks registration', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'coupon' => 'UNKNOWN',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('register'));
    $response->assertSessionHasErrors('coupon');
    expect(Coupon::count())->toBe(0);
});

test('claimed coupon blocks registration', function () {
    $coupon = Coupon::factory()->claimed()->create([
        'id' => 'GDUNIV_qCP0flx0',
        'email' => 'student@example.com',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'coupon' => $coupon->id,
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('register'));
    $response->assertSessionHasErrors('coupon');
    expect($coupon->fresh()->user_id)->toBeNull();
});

test('coupon email mismatch is allowed', function () {
    $coupon = Coupon::factory()->create([
        'id' => 'GDUNIV_qCP0flx0',
        'email' => 'student@example.com',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'other@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'coupon' => $coupon->id,
    ]);

    $response->assertRedirect();

    $coupon->refresh();
    expect($coupon->user_id)->not->toBeNull();
    expect($coupon->claimed_at)->not->toBeNull();
    expect($coupon->user->premium_end_at)->not->toBeNull();
    expect($coupon->user->premium_end_at->isFuture())->toBeTrue();
});
