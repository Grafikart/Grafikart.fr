@extends('front')

@section('body')
    <div class="container mb-10">
        <h1 class="text-6xl font-bold font-serif mb-4 text-foreground-title">
            Apprenez avec nos <br><span class="text-primary">parcours personnalisés</span>
        </h1>
        <p class="text-xl text-balance">
            Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
            Alors vous êtes sur le bon chemin...
        </p>
    </div>

    <div>
        @for($i = 0; $i < 4; $i++)
            @foreach($paths as $path)
                <div class="border relative grid grid-cols-1">
                    <div
                        class="absolute z-10 inset-0 bg-linear-to-b from-background/80 to-background/10 to-[120px]">
                        <div class="container py-4 text-balance">
                            <h2 class="text-4xl font-bold text-foreground-title mb-1 w-1/2">
                                {{ $path->title }}
                            </h2>
                            <p class="mb-4 text-muted w-1/2 text-pretty">
                                {{ $path->description }}
                            </p>
                            <x-atoms.button href="{{ route('paths.show', ['slug' => $path->slug, 'path' => $path->id])  }}">
                                Commencer ce parcours
                            </x-atoms.button>
                        </div>
                    </div>
                    <path-preview
                        class="h-100 block"
                        path="{{ $path->toJson() }}"/>
                </div>
            @endforeach
        @endfor
    </div>
@endsection
