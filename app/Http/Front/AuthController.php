<?php

namespace App\Http\Front;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController
{
    const DRIVERS = ['github', 'google', 'facebook'];

    public function connect(string $driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function callback(string $driver)
    {
        assert(in_array($driver, self::DRIVERS));
        $oauthUser = Socialite::driver($driver)->user();

        // Find the user based on the token
        $user = $this->findUser($driver, $oauthUser);
        if (! $user) {
            return to_route('home')->with('error', "Impossible de vous authentifier avec {$driver}");
        }

        Auth::login($user);

        return to_route('home');
    }

    /**
     * Resolve a User from an oauth user
     */
    private function findUser(string $driver, \Laravel\Socialite\Contracts\User $oauthUser): ?User
    {
        $field = "{$driver}_id";
        $oauthId = $oauthUser->getId();
        if (! $oauthId) {
            return null;
        }
        // Find the user using its oauth_id
        $user = User::where($field, $oauthUser->getId())->first();
        if ($user) {
            return $user;
        }

        // Find a corresponding user using the email
        $email = $oauthUser->getEmail();
        if (! $email) {
            return null;
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->setAttribute($field, $oauthUser->getId());
            $user->save();

            return $user;
        }

        // Create the user on the fly
        $name = $oauthUser->getName();
        if (! $name) {
            return null;
        }
        $suffix = User::where('name', $name)->exists() ? '_'.Str::random(4) : '';

        return User::forceCreate([
            'name' => "{$name}{$suffix}",
            'email' => $email,
            'email_verified_at' => now(),
            'password' => '',
            'country' => 'FR',
            'notifications_read_at' => now(),
            $field => $oauthId,
        ]);
    }

    /**
     * Handle checking permission for the forward_auth (caddy) / auth_request (nginx)
     */
    public function checkPremium(): Response | RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return to_route('login')->with('error', 'Vous devez être premium pour accéder à ce contenu');
        }
        if ($user && $user->isPremium()) {
            return response()->noContent();
        }

        abort(403, 'Vous devez être premium pour télécharger le contenu');
    }


}
