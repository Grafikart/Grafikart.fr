@extends('front')

@section('title', 'Tutoriels et Formations vidéos sur le développement web')

@section('head')
    <style>
        section.container {
            background: var(--background-light);
        }

        section.container:nth-child(odd) {
            background: var(--background);
        }
    </style>
@endsection

@section('body')

    @php
    $duration = \App\Domains\Course\Course::totalHours();
    $formations = $queries['formations']->get();
    $posts = $queries['posts']->get();
    $courses = $queries['courses']->get();
    $paths = App\Http\Front\Data\PathViewData::collect($queries['paths']->get());
    $plans = \App\Domains\Premium\Models\Plan::all();
    @endphp

    <section class="py-17.5 md:py-30! container bg-background-light grid gap-8 md:gap-12 md:grid-cols-[400px_1fr] items-center">
        <div>
            <h1 class="text-6xl md:text-7xl font-bold font-serif mb-4 text-foreground-title">
                <span class="text-primary">Apprenez</span> de<br/>
                nouvelles<br/>
                choses. </h1>
            <p class="text-2xl text-balance text-muted">
                Améliorez-vous et apprenez de nouvelles choses grâce à <strong class="text-foreground">{{ $duration }}</strong> heures de vidéos.
            </p>
            <div class="flex gap-4 mt-4 flex-wrap">
                <x-atoms.button size="lg" href="{{ route('paths.index') }}">
                    Choisir mon cursus
                </x-atoms.button>
                <x-atoms.button size="lg" variant="outline" href="{{ route('courses.index') }}">
                    Voir les tutoriels
                </x-atoms.button>
            </div>
        </div>
        <x-atoms.lazy-video video="UmQDMqAjZiw" class="w-full max-w-200 ml-auto"/>
    </section>

    <section class="container pt-17 grid gap-8 lg:grid lg:grid-cols-[480fr_800px] lg:gap-0 items-start">
        <div>
            <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance">
                Apprendre grâce à
                <div class="text-primary"> plusieurs formats
                </div>
            </h2>
            <p class="text-2xl text-pretty text-muted mt-4">
                Vous cherchez une formation complète pour apprendre de A à Z ou une vidéo pour découvrir un nouvel outil
                ?
                Vous devriez trouver votre bonheur
            </p>
        </div>
        <div class="grid gap-10 lg:grid-cols-40 lg:gap-0 lg:items-start lg:gap-y-5 xl:-ml-40">
            <x-atoms.card class="px-6 py-4 relative lg:col-[16/span_22]">
                <h2 class="font-bold text-warning-text bg-warning-bg/70 rounded-full border-warning-text/20 border -top-4 left-2 px-3 py-1 absolute">
                    Les dernières formations</h2>
                <div class="divide-y">
                    @foreach($formations as $formation)
                        <div class="flex gap-4 items-center py-4 relative">
                            <img src="{{ $formation->icon() }}" alt="" class="w-10">
                            <div class="">
                                <h3 class="font-bold text-lg">
                                    <a href="{{ app_url($formation) }}" class="overlay hover:text-primary">
                                        {{ $formation->title }}
                                    </a>
                                </h3>
                                <p class="text-muted">{{ $formation->course_ids->count() }} chapitres</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-atoms.card>

            <x-atoms.card class="px-6 py-4 relative lg:col-span-23">
                <h2 class="font-bold text-info-text bg-info-bg/70 rounded-full border-info-text/20 border -top-4 left-2 px-3 py-1 absolute">
                    Les dernières formations</h2>
                <div class="divide-y">
                    @foreach($courses as $course)
                        <div class="flex gap-4 items-center py-4 relative">
                            <img src="{{ $course->technology()?->mediaUrl('image') }}" alt="" class="w-10">
                            <div class="">
                                <h3 class="font-bold text-lg">
                                    <a href="{{ app_url($course) }}" class="overlay hover:text-primary">
                                        {{ $course->title }}
                                    </a>
                                </h3>
                                <p class="text-muted">
                                    <x-atoms.duration :duration="$course->duration"/>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-atoms.card>

            <x-atoms.card class="px-6 py-4 relative lg:col-[25/-1]">
                <h2 class="font-bold text-success-text bg-info-bg/70 rounded-full border-info-text/20 border -top-4 left-2 px-3 py-1 absolute">
                    Les derniers articles</h2>
                <div class="divide-y">
                    @foreach($posts->slice(0, 3) as $post)
                        <div class="items-center py-4 relative">
                            <h3 class="font-bold text-lg">
                                <a href="{{ app_url($post) }}" class="overlay hover:text-primary">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-muted">
                                <x-atoms.ago :date="$post->created_at"/>
                            </p>
                        </div>
                    @endforeach
                </div>
            </x-atoms.card>
        </div>
    </section>

    <section class="container -mt-22 pt-40 pb-20">
        <div class="flex flex-col md:flex-row gap-4 md:items-end md:gap-10 mb-8">
            <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance w-max flex-none">
                Choisissez votre
                <div class="text-primary">prochain parcours</div>
            </h2>
            <p class="text-2xl text-pretty text-muted mt-4">
                Chaque parcours est conçu pour vous aider à construire des bases solides, découvrir des outils modernes et gagner en autonomie.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach($paths as $path)
                <x-molecules.path-card :path="$path"/>
            @endforeach
        </div>
    </section>


    <section class="container pt-20">
        <h2 class="text-5xl md:text-6xl font-serif font-bold text-center mb-6 text-foreground-title">
            Devenir premium
        </h2>
        <p class="text-xl text-muted md:text-xl text-center max-w-200 mx-auto leading-8 mb-8">
            Devenir premium sur Grafikart, c'est <strong class="text-foreground">soutenir</strong> la création de nouveaux contenus chaque
            semaine et accéder à du contenu exclusif pour apprendre et s'améliorer (comme le téléchargement des vidéos
            et des sources).
        </p>

        <x-organisms.premium class="z-3 relative" :plans="$plans"/>

    </section>

    <section class="container -mt-15 gap-8 pt-35 pb-25 bg-background grid md:grid-cols-[29fr_29fr_42fr] items-end">
        <div class="flex flex-col">
            <h2 class="text-5xl lg:text-6xl font-serif font-bold mb-6 text-foreground-title">
                Des nouvelles du blog
            </h2>
            <p class="text-lg lg:text-xl leading-8 mb-6">Venez découvrir les actualités autour de l'univers du
                développement web.
            </p>
            <p class="mb-10">
                <x-atoms.button size="lg" variant="outline" href="{{ route('courses.index') }}">
                    Accéder au blog
                </x-atoms.button>
            </p>
            <x-molecules.home-post-card :post="$posts[0]" :index="0" class="mt-auto"/>
        </div>
        <x-molecules.home-post-card :post="$posts[1]" :index="1"/>
        <div class="grid gap-8">
            @foreach($posts->slice(2) as $k => $post)
                <x-molecules.home-post-card :post="$post" :index="$k"/>
            @endforeach
        </div>
    </section>

@endsection
