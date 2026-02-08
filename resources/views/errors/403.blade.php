@extends('front')

@section('title', 'Accès interdit')

@section('body')
    <div class="container pb-10 bg-background-light">
        <h2 class="text-page-title mb-4">Accès interdit</h2>
        <p class="text-xl text-muted max-w-2xl mx-auto">
            @if(isset($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
            @else
                Vous n'avez pas l'autorisation d'accéder à cette page.<br/>
                Cette zone est réservée aux utilisateurs autorisés.
            @endif
        </p>
    </div>

    <div class="bg-background border-t pt-15 container flex flex-col gap-5 items-center">
        <iframe src="https://giphy.com/embed/0jFCBNtcpQXrCaNg8e" width="480" height="480" style="" frameBorder="0"
                class="aspect-square border-card border-5 max-w-full h-auto mx-auto pointer-events-none"
                allowFullScreen></iframe>
        <x-atoms.button
            variant="secondary"
            href="/"
        >
            <x-lucide-home class="size-5"/>
            Revenir à l'accueil
        </x-atoms.button>
    </div>
@endsection
