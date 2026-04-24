@extends('front')

@section('title', 'Se connecter')

@section('body')
    <div class="px-4 mx-auto mt-10 max-w-100 pb-20">
        <h1 class="text-page-title text-center mb-8">Se connecter</h1>

        <x-atoms.card class="p-6">
            @if($status ?? false)
                <div
                    class="mb-4 rounded-md bg-success-50 p-3 text-sm text-success-700 dark:bg-success-900/20 dark:text-success-400">
                    {{ $status }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <x-molecules.field name="email" label="Email ou nom d'utilisateur" required autofocus
                                   autocomplete="username"/>
                <x-molecules.field name="password" label="Mot de passe" type="password" required
                                   autocomplete="current-password">
                    <x-slot:after-label>
                        @if($canResetPassword ?? false)
                            <a href="{{ route('password.request') }}" tabindex="-1" class="text-xs text-primary hover:underline">
                                Mot de passe oublié ?
                            </a>
                        @endif
                    </x-slot:after-label>
                </x-molecules.field>

                <div class="flex items-center gap-2">
                    <x-atoms.checkbox id="remember" name="remember" :checked="old('remember')"/>
                    <x-atoms.label for="remember">Se souvenir de moi</x-atoms.label>
                </div>

                <x-atoms.button type="submit" class="w-full">
                    Se connecter
                    <x-lucide-arrow-right class="size-4"/>
                </x-atoms.button>
            </form>

            @if($canRegister ?? false)
                <div class="mt-6 border-t pt-4 text-center text-sm text-muted italic">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-primary hover:underline">
                        Créer un compte
                    </a>
                </div>
            @endif
        </x-atoms.card>

        @include('auth.oauth')

    </div>
@endsection
