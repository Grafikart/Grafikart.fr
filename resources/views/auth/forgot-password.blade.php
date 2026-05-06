@extends('front')

@section('title', 'Mot de passe oublié')

@section('body')
    <div class="px-4 mt-10 mx-auto max-w-100 pb-20">
            <h1 class="text-page-title text-center mb-8">Mot de passe oublié</h1>

            <x-atoms.card class="p-6">

                @if($status ?? false)
                    <div class="mb-4 rounded-md bg-success-50 p-3 text-sm text-success-700 dark:bg-success-900/20 dark:text-success-400">
                        {{ $status }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <x-molecules.field label="Votre email" name="email" type="email" required autofocus autocomplete="username" />

                    <x-atoms.button type="submit" class="w-full!">
                        Envoyer le lien de réinitialisation
                        <x-lucide-send class="size-4" />
                    </x-atoms.button>
                </form>

            </x-atoms.card>

            <div class="mt-4 text-center text-sm text-muted italic">
                <a href="{{ route('login') }}" class="text-muted hover:underline">
                    Retour à la connexion
                </a>
            </div>
    </div>
@endsection
