<?php

use App\Domains\Coupon\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Event;

describe('claim', function () {
    it('allows an authenticated user to claim a coupon', function () {
        Event::fake();

        $user = User::factory()->create(['id' => 1]);
        $coupon = Coupon::factory()->create([
            'id' => 'GDUNIV_qCP0flx0',
            'months' => 3,
        ]);

        $this->actingAs($user)
            ->post(route('users.coupon'), [
                'coupon' => $coupon->id,
            ])
            ->assertRedirect(route('users.edit'))
            ->assertSessionHas('success');

        $coupon->refresh();
        $user->refresh();

        expect($coupon->user_id)->toBe($user->id);
        expect($coupon->claimed_at)->not->toBeNull();
        expect($user->premium_end_at)->not->toBeNull();
    });

    it('returns validation error for an invalid coupon', function () {
        $user = User::factory()->create(['id' => 1]);

        $this->actingAs($user)
            ->from(route('users.edit'))
            ->post(route('users.coupon'), [
                'coupon' => 'UNKNOWN',
            ])
            ->assertRedirect(route('users.edit'))
            ->assertInvalid(['coupon']);
    });

    it('returns validation error for an already claimed coupon', function () {
        $user = User::factory()->create(['id' => 1]);
        $coupon = Coupon::factory()->claimed()->create();

        $this->actingAs($user)
            ->from(route('users.edit'))
            ->post(route('users.coupon'), [
                'coupon' => $coupon->id,
            ])
            ->assertRedirect(route('users.edit'))
            ->assertInvalid(['coupon']);
    });

});
