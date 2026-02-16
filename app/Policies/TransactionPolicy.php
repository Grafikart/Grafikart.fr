<?php

namespace App\Policies;

use App\Domains\Premium\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }
}
