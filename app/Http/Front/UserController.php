<?php

namespace App\Http\Front;

use App\Domains\Account\AccountService;
use App\Domains\Account\Data\UserDeletionRequestData;
use App\Domains\Account\Exceptions\PasswordMismatchException;
use App\Domains\Badge\BadgeRepository;
use App\Domains\History\ProgressRepository;
use App\Http\Front\Data\User\PasswordUpdateData;
use App\Http\Front\Data\User\ProfileUpdateData;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController
{
    public function edit(\Illuminate\Http\Request $request): View
    {
        $user = $request->user();
        assert($user instanceof User);
        $errors = $request->session()->get('errors');
        $status = $request->session()->get('status');

        if ($request->boolean('verified')) {
            $request->session()->flash('success', 'Votre email a bien été confirmé');
        }

        if ($status === 'two-factor-authentication-disabled') {
            $request->session()->flash('success', "L'authentification à 2 facteurs a bien été désactivée");
        }

        if ($status === 'two-factor-authentication-confirmed') {
            $request->session()->flash('success', "L'authentification à 2 facteurs a bien été activée");
        }

        $twoFactorPending = $status === 'two-factor-authentication-enabled' || $errors?->hasBag('confirmTwoFactorAuthentication');
        $twoFactorConfirmed = $status === 'two-factor-authentication-confirmed' || $status === 'recovery-codes-generated';

        if ($twoFactorPending) {
            return view('users.2fa-setup', [
                'user' => $user,
                'twoFactorQrCode' => $user->twoFactorQrCodeSvg(),
            ]);
        }

        if ($twoFactorConfirmed) {
            return view('users.2fa-codes', [
                'user' => $user,
                'codes' => $user->recoveryCodes(),
            ]);
        }

        $twoFactorEnabled = $user->hasEnabledTwoFactorAuthentication();

        return view('users.edit', [
            'user' => $user,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'twoFactorEnabled' => $twoFactorEnabled,
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

    public function delete(UserDeletionRequestData $data, AccountService $account, Request $request): RedirectResponse
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

    public function history(Request $request, ProgressRepository $repository): View
    {
        $user = $request->user();
        $type = $request->query('type', 'course');
        if (! in_array($type, ['course', 'formation'])) {
            throw new NotFoundHttpException("Impossible de trouver l'historique associé à ce contenu");
        }
        assert($user instanceof User);

        return view('users.history', [
            'items' => $repository->findItemsForUser($user->id, $type),
            'type' => $type,
        ]);
    }

    public function badges(Request $request, BadgeRepository $repository): View
    {
        $user = $request->user();
        assert($user instanceof User);

        return view('users.badges', [
            'badges' => $repository->forUser($user->id),
        ]);
    }
}
