@extends('errors.layout')

@section('title', 'Maintenance')

@section('body')
    <div class="container pb-10 bg-background-light min-h-100 h-screen flex flex-col items-start justify-center">
        <div class="grid xl:gap-0 xl:grid-cols-[max-content_1fr] justify-items-center gap-10 w-full mx-auto min-w-0">
            <div>
                <p class="text-sm font-semibold uppercase text-primary mb-3">Erreur 503</p>
                <h2 class="text-page-title mb-4">Maintenance en cours</h2>
                <p class="text-xl text-muted max-w-2xl mb-4">
                    Le site est temporairement indisponible le temps d'une mise à jour.<br/>
                    Cela ne devrait pas prendre plus d'une minute.
                </p>
                <x-atoms.button
                    variant="secondary"
                    href="/"
                >
                    <x-lucide-refresh-cw class="size-5"/>
                    Rafraîchir
                </x-atoms.button>
            </div>
            <img src="/images/illustrations/maintenance.webp" alt="" class="w-full min-w-0 max-w-200 xl:-ml-10" width="1507" height="857">
        </div>
    </div>
@endsection
