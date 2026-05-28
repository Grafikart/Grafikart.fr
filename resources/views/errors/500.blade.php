@extends('errors.layout')

@section('title', 'Erreur serveur')

@section('body')
    <div class="container pb-10 bg-background-light min-h-100 h-screen flex flex-col items-start justify-center">
        <div class="grid lg:gap-0 xl:grid-cols-[max-content_1fr] justify-items-center items-center gap-10 w-full mx-auto min-w-0">
            <div>
                <p class="text-sm font-semibold uppercase text-primary mb-3">Erreur 500</p>
                <h2 class="text-page-title mb-4">Erreur serveur interne</h2>
                <p class="text-xl text-muted max-w-2xl mb-4">
                    Une erreur inattendue est survenue sur le serveur.<br/>
                    Vous pouvez réessayer dans quelques instants.
                </p>
            </div>
            <img src="/images/illustrations/server-error.webp" alt="" class="w-full min-w-0 max-w-170" width="1507" height="857">
        </div>
    </div>
@endsection
