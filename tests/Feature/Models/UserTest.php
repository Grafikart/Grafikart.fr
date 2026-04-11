<?php

use App\Models\User;

describe('extendsPremium', function () {
    it('starts premium from now when user is not premium', function () {
        $user = User::factory()->make([
            'premium_end_at' => null,
        ]);

        $user->extendsPremium(3);

        expect($user->premium_end_at)->not->toBeNull();
        expect($user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(3)->format('Y-m-d'));
    });

    it('starts premium from now when current premium is expired', function () {
        $user = User::factory()->make([
            'premium_end_at' => now()->subMonth(),
        ]);

        $user->extendsPremium(2);

        expect($user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(2)->format('Y-m-d'));
    });

    it('extends from existing end date when user is already premium', function () {
        $user = User::factory()->make([
            'premium_end_at' => now()->addMonths(2),
        ]);

        $user->extendsPremium(3);

        expect($user->premium_end_at->format('Y-m-d'))->toBe(now()->addMonths(5)->format('Y-m-d'));
    });
});
