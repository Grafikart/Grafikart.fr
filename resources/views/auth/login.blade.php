@extends('front')

@section('title', 'Se connecter')

@section('body')
    <div class="container mt-10 mx-auto max-w-100">
            <h1 class="text-page-title text-center mb-8">Se connecter</h1>

            <x-atoms.card class="p-6">
                @if($status ?? false)
                    <div
                        class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
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
                                <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline">
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

                <x-atoms.separator/>

                @if($canRegister ?? false)
                    <div class="mt-6 border-t pt-4 text-center text-sm text-muted italic">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-primary hover:underline">
                            Créer un compte
                        </a>
                    </div>
                @endif
            </x-atoms.card>

            <h2 class="mt-8 mb-4 text-foreground-title font-serif font-semibold text-3xl">Utiliser les réseaux
                sociaux</h2>

            <div class="space-y-4">
                <x-atoms.button size="lg" class="bg-[#444]  w-full relative">
                    <x-lucide-github class="absolute left-5 top-1/2 -translate-y-1/2"/>
                    Se connecter avec GitHub
                </x-atoms.button>

                <x-atoms.button size="lg" class="bg-[#dd4b39]  w-full relative">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 48 48"
                         class="absolute left-5 top-1/2 -translate-y-1/2 size-4">
                        <path
                            fill="currentColor"
                            d="M24.7 20.5v7.6h10.9a10.9 10.9 0 0 1-10.9 8 12.1 12.1 0 1 1 7.9-21.3l5.6-5.6A20 20 0 1 0 24.7 44c16.8 0 20.5-15.7 18.9-23.5Z"

                        />
                    </svg>
                    Se connecter avec Google
                </x-atoms.button>

                <x-atoms.button size="lg" class="bg-[#47639e]  w-full relative">
                    <x-lucide-facebook class="absolute left-5 top-1/2 -translate-y-1/2"/>
                    Se connecter avec Facebook
                </x-atoms.button>
            </div>
    </div>
@endsection
