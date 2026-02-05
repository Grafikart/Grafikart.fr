@extends('front')

@section('title', 'Réinitialiser le mot de passe')

@section('body')
    <div class="container mt-10 mx-auto max-w-100">
            <h1 class="text-6xl font-serif font-bold text-foreground-title font text-center mb-8">Nouveau mot de passe</h1>

            <x-atoms.card class="p-6">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <x-molecules.field name="email" type="email" :value="$email" required autofocus autocomplete="username" />
                    <x-molecules.field name="password" label="Nouveau mot de passe" type="password" required autocomplete="new-password" />
                    <x-molecules.field name="password_confirmation" label="Confirmer le mot de passe" type="password" required autocomplete="new-password" />

                    <x-atoms.button type="submit" class="w-full">
                        Réinitialiser le mot de passe
                        <x-lucide-arrow-right class="size-4" />
                    </x-atoms.button>
                </form>
            </x-atoms.card>
    </div>
@endsection
