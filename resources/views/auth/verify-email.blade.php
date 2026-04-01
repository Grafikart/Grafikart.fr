@extends('front')

@section('title', 'Vérifier votre email')

@section('body')
    <div class="px-4 my-10 mx-auto max-w-100 pb-20">
            <h1 class="text-page-title text-center mb-8">Vérifiez votre email</h1>

            <x-atoms.card class="p-6">
                <p class="mb-4 text-sm text-muted">
                    Merci de votre inscription ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                </p>

                @if($status == 'verification-link-sent')
                    <div class="mb-4 rounded-md bg-success-50 p-3 text-sm text-success-700 dark:bg-success-900/20 dark:text-success-400">
                        Un nouveau lien de vérification a été envoyé à votre adresse email.
                    </div>
                @endif

                <div class="flex flex-col gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-atoms.button type="submit" class="w-full">
                            Renvoyer l'email de vérification
                            <x-lucide-send class="size-4" />
                        </x-atoms.button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-atoms.button type="submit" variant="ghost" class="w-full">
                            Se déconnecter
                        </x-atoms.button>
                    </form>
                </div>
            </x-atoms.card>
    </div>
@endsection
