@extends('front')

@section('title', "Parcours d'apprentissage")

@section('body')
    <section class="container pb-15 bg-background-light">
        <h1 class="text-6xl font-bold font-serif mb-4 text-foreground-title">
            Apprenez avec nos <br><span class="text-primary">parcours personnalisés</span>
        </h1>
        <p class="text-xl text-balance">
            Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
            Alors vous êtes sur le bon chemin...
        </p>
    </section>

    <section class="container py-15">

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
                <x-molecules.path-card :path="$path"/>
            @endforeach
        </div>
    </section>

    <section class="bg-background-light container py-20">
        <div class="max-w-175 mx-auto">
            <h2 class="text-5xl font-serif font-bold mb-4 text-foreground-title">
                Vous ne savez pas <br/>
                <span class="text-primary">
                   quoi choisir ?
                </span>
            </h2>
            <div class="prose prose-lg max-w-175 mx-auto">
                <p>
                    Frontend, Backend, Fullstack ? Si tous ces termes ne vous parlent pas, vous pouvez suivre cette petite vidéo qui vous permettra d'y voir plus clair et de
                    choisir le parcours qui vous conviendra.
                </p>
                <p>
                    La plupart de ces parcours ont le même point de départ (<a href="/formations/html">HTML</a>, <a href="/formations/apprendre-css">CSS</a>...), vous pouvez commencer par cette base et choisir votre parcours dans un second temps
                </p>
            </div>
        </div>
        <x-atoms.lazy-video video="3yWEsqqyvp4" class="my-8 max-w-250 mx-auto"/>
    </section>
@endsection
