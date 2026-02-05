@extends('front')

@section('title', 'Confirmer le mot de passe')

@section('body')
    <div class="container mt-10 mx-auto max-w-100">
            <h1 class="text-6xl font-serif font-bold text-foreground-title font text-center mb-8">Confirmer le mot de passe</h1>

            <x-atoms.card class="p-6">
                <p class="mb-4 text-sm text-muted">
                    Cette zone est sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
                </p>

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                    @csrf

                    <x-molecules.field name="password" label="Mot de passe" type="password" required autofocus autocomplete="current-password" />

                    <x-atoms.button type="submit" class="w-full">
                        Confirmer
                        <x-lucide-check class="size-4" />
                    </x-atoms.button>
                </form>
            </x-atoms.card>
    </div>
@endsection
