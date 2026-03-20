@extends('front')

@section('body')
    <div class="container pb-15 bg-background-light">
        <h1 class="text-6xl font-bold font-serif mb-4 text-foreground-title">
            Apprenez avec nos <br><span class="text-primary">parcours personnalisés</span>
        </h1>
        <p class="text-xl text-balance">
            Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
            Alors vous êtes sur le bon chemin...
        </p>
        {{--<div class="grid md:grid-cols-3 max-w-200 gap-5 mt-6">
            <div class="bg-background border rounded-lg p-3 py-2.5">
                <h2 class="font-bold text-lg mb-1">{{ count($paths) }} parcours</h2>
                <p>
                    Une sélection pensée pour progresser étape par étape
                </p>
            </div>

            <div class="bg-background border rounded-lg p-3 py-2.5">
                <h2 class="font-bold text-lg mb-1">Du concret</h2>
                <p>
                    Des compétences utiles pour travailler sur de vrais projets
                </p>
            </div>

            <div class="bg-background border rounded-lg p-3 py-2.5">
                <h2 class="font-bold text-lg mb-1"> À votre rythme </h2>
                <p> Commencez avec les bases puis avancez vers la pratique. </p>
            </div>
        </div>--}}
    </div>

    <div class="container py-15">

        <div class="flex flex-col md:flex-row gap-4 items-end mb-8">
            <h2 class="text-4xl font-serif font-bold text-balance max-w-100 text-foreground-title">Choisissez votre
                prochain parcours</h2>
            <p class="text-lg text-muted">
                Chaque parcours est conçu pour vous aider à construire des bases solides, découvrir des outils modernes
                et gagner en autonomie.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-5">
            @foreach($paths as $path)
                <x-atoms.card class="flex flex-col p-5 rounded-xl hover:shadow-lg relative transition group">
                    <h2 class="text-xl leading-tight font-bold text-foreground-title mb-3">
                        <a
                            class="overlay hover:text-primary"
                            href="{{ app_url($path) }}">
                            {{ $path->title }}
                        </a>
                    </h2>
                    <p class="mb-4 text-muted text-pretty">
                        {{ $path->description }}
                    </p>
                    <div class="flex items-center flex-wrap gap-2">
                        @foreach($path->tags() as $tag)
                            <div
                                class="bg-border/20 w-max rounded-xl p-2 py-1 uppercase text-xs text-muted group-hover:bg-primary/10 group-hover:text-primary">
                                #{{ $tag }}
                            </div>
                        @endforeach
                    </div>
                </x-atoms.card>
            @endforeach
        </div>
    </div>
@endsection
