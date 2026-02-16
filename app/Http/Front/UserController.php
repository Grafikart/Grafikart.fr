<?php

namespace App\Http\Front;

use App\Domains\Account\AccountService;
use App\Domains\Account\Data\UserDeletionRequestData;
use App\Domains\Account\Exceptions\PasswordMismatchException;
use App\Http\Front\Data\User\PasswordUpdateData;
use App\Http\Front\Data\User\ProfileUpdateData;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController
{
    public function me()
    {
        return 'me';
    }

    public function edit(\Illuminate\Http\Request $request): View
    {
        $user = $request->user();
        assert($user instanceof User);

        if ($request->boolean('verified')) {
            $request->session()->flash('success', 'Votre email a bien été confirmé');
        }

        return view('users.edit', [
            'user' => $user,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
        ]);
    }

    public function update(ProfileUpdateData $data, Request $request)
    {
        $user = $request->user();
        assert($user instanceof User);
        $user->fill($data->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        return to_route('users.edit')->with('success', 'Votre profil a bien été mis à jour');
    }

    public function password(PasswordUpdateData $data, Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $user->password = Hash::make($data->password);

        return to_route('users.edit')->with('success', 'Votre mot de passe a bien été mis à jour');
    }

    public function delete(UserDeletionRequestData $data, AccountService $account,  Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        try {
            $account->deleteUser($user, $data);
        } catch (PasswordMismatchException) {
            return to_route('users.edit')->with('error', 'Mot de passe incorrect');
        }
        Auth::logout();
        return to_route('home')->with('success', 'Votre compte a bien été supprimé');
    }
}
