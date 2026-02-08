<?php

use App\Models\User;

describe('checkPremium', function () {
    it('returns 403 for guests', function () {
        $this->get('/auth/check/premium')->assertForbidden();
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
