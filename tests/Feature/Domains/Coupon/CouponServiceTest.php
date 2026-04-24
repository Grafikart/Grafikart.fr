<?php

use App\Domains\Coupon\Coupon;
use App\Domains\Coupon\CouponService;
use App\Domains\Premium\Event\PremiumSubscriptionEvent;
use App\Models\User;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->service = app(CouponService::class);
});

describe('claim', function () {
    it('claims coupon and extends user premium', function () {
        Event::fake([PremiumSubscriptionEvent::class]);

        $user = User::factory()->create([
            'email' => 'student@example.com',
        ]);
        $coupon = Coupon::factory()->create([
            'id' => 'GDUNIV_qCP0flx0',
            'email' => 'student@example.com',
            'months' => 3,
        ]);

        $this->service->claim($coupon->id, $user);

        $user->refresh();
        $coupon->refresh();

        expect($coupon->user_id)->toBe($user->id);
        expect($coupon->claimed_at)->not->toBeNull();
        expect($user->premium_end_at)->not->toBeNull();
        expect($user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(3)->format('Y-m-d'));
    });

    it('extends from current premium end date when user is already premium', function () {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'premium_end_at' => now()->addMonths(2),
        ]);
        $coupon = Coupon::factory()->create([
            'email' => 'student@example.com',
            'months' => 3,
        ]);

        $this->service->claim($coupon->id, $user);

        $user->refresh();

        expect($user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(5)->format('Y-m-d'));
    });

    it('throws when coupon does not exist', function () {
        $user = User::factory()->create([
            'email' => 'student@example.com',
        ]);

        expect(fn () => $this->service->claim('UNKNOWN', $user))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });

});
