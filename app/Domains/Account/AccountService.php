<?php

namespace App\Domains\Account;

use App\Domains\Account\Data\UserDeletionRequestData;
use App\Domains\Account\Events\UserDeletedEvent;
use App\Domains\Account\Exceptions\PasswordMismatchException;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function __construct(private readonly StatefulGuard $auth) {}

    public function deleteUser(User $user, UserDeletionRequestData $data): void
    {
        if (! Hash::check($data->password, $user->password)) {
            throw new PasswordMismatchException;
        }

        $user->delete();
        event(new UserDeletedEvent($user, $data->reason));
        $this->auth->logout();
    }
}
