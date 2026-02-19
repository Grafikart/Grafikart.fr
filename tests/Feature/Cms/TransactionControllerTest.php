<?php

use App\Domains\Premium\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

describe('index', function () {
    it('paginates transactions', function () {
        Transaction::factory()->count(20)->create();

        $this->actingAs($this->admin)
            ->get(route('cms.transactions.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('transactions/index')
                ->has('pagination.data', 15)
            );
    });

    it('filters transactions by user id', function () {
        $user = User::factory()->create();
        Transaction::factory()->count(3)->create(['user_id' => $user->id]);
        Transaction::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('cms.transactions.index', ['q' => "user:{$user->id}"]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('transactions/index')
                ->has('pagination.data', 3)
                ->where('q', "user:{$user->id}")
            );
    });

    it('filters transactions by method_id', function () {
        Transaction::factory()->count(2)->create(['method_id' => 'stripe_abc123']);
        Transaction::factory()->count(3)->create(['method_id' => 'paypal_xyz']);

        $this->actingAs($this->admin)
            ->get(route('cms.transactions.index', ['q' => 'stripe']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('transactions/index')
                ->has('pagination.data', 2)
                ->where('q', 'stripe')
            );
    });
});

describe('destroy (refund)', function () {
    it('marks a transaction as refunded', function () {
        $transaction = Transaction::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('cms.transactions.destroy', $transaction))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertNotNull($transaction->fresh()->refunded_at);
    });

    it('does not delete the transaction', function () {
        $transaction = Transaction::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('cms.transactions.destroy', $transaction));

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    });
});
