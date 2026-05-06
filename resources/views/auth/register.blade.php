@extends('front')

@section('title', 'Créer un compte')

@section('body')
    <div class="px-4 mt-10 mx-auto max-w-100 pb-20">
            <h1 class="text-5xl font-serif font-bold text-foreground-title text-center mb-8">Créer un compte</h1>

            <x-atoms.card class="p-6">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <x-molecules.field name="name" label="Nom d'utilisateur" required autofocus autocomplete="name" />
                    <x-molecules.field name="email" type="email" :value="$email ?? ''" required autocomplete="username" />
                    <x-molecules.field name="password" label="Mot de passe" type="password" required autocomplete="new-password" />
                    <x-molecules.field name="password_confirmation" label="Confirmer le mot de passe" type="password" required autocomplete="new-password" />
                    @if(($coupon ?? '') !== '' || old('coupon'))
                        <x-molecules.field name="coupon" label="Code étudiant" :value="$coupon ?? ''" readonly />
                    @endif
                    <x-molecules.captcha />
                    <x-atoms.button type="submit" class="w-full!">
                        Créer mon compte
                        <x-lucide-arrow-right class="size-4" />
                    </x-atoms.button>

                    <p class="italic text-xs text-muted">
                        Vos données personnelles (email et nom d'utilisateur) ne sont utilisées qu'à des fins d'authentification et ne sont pas partagées avec des tiers (<a href="/politique-de-confidentialite" class="underline">En savoir plus</a>).
                    </p>
                </form>

            </x-atoms.card>

            <div class="mt-4 pt-4 text-center text-sm text-muted italic">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="text-primary hover:underline">
                    Se connecter
                </a>
            </div>

        @include('auth.oauth')
    </div>
@endsection
