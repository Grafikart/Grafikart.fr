@extends('front')

@section('title', 'Apprendre ' . $technology->name)

@section('body')

    <div class="container pb-10 bg-background-light grid grid-cols-1 md:grid-cols-[1fr_400px] gap-4">
        <div class="row-2 md:row-1">
            <h1 class="text-page-title mb-4">{{ $technology->name }}</h1>
            <div class="text-lg text-muted max-w-175">
                {!! \App\Helpers\MarkdownHelper::html($technology->content) !!}
            </div>
        </div>
        <img
            src="{{ $technology->mediaUrl('image') }}"
            alt="{{ $technology->name }}"
            class="size-30 md:size-50 object-contain md:mx-auto row-1"
        />
    </div>

    <div class="border-t bg-background pt-10 container grid grid-cols-1 md:grid-cols-[1fr_400px] gap-10">
        <main class="space-y-8">
            @if($isEmpty)
                <x-atoms.alert type="info">
                    Il n'y a pas encore de contenu pour cette technologie :(
                </x-atoms.alert>
            @else
                @foreach($formations as $level => $items)
                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold text-foreground-title">
                            {{ $level === 0 ? 'Apprendre les bases' : 'Se perfectionner' }}
                        </h2>
                        <div class="space-y-2">
                            @foreach($items as $formation)
                                <x-molecules.formation-card :formation="$formation"/>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-foreground-title">
                        Découvrir {{ $technology->name }} avec des tutoriels
                    </h2>
                    <div class="grid grid-fill-261 gap-6 grid-flow-row-dense">
                        @foreach($courses as $course)
                            <x-molecules.course-card :course="$course"/>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    {{ $courses->links() }}
                </div>
            @endif
        </main>

        <aside class="space-y-8">
            @if($requirements->isNotEmpty())
                <section class="space-y-3">
                    <h2 class="text-xl font-bold text-foreground-title">Ce qu'il faut connaître</h2>
                    <p class="text-muted">
                        Avant de pouvoir apprendre {{ $technology->name }}, il est important d'avoir acquis les connaissances suivantes :
                    </p>
                    <div class="flex flex-col divide-y divide-border border-y border-border overflow-hidden">
                        @foreach($requirements as $requirement)
                            <a href="{{ route('technologies.show', $requirement) }}" class="hover:pl-2 py-2 hover:bg-list-hover transition-all">
                                {{ $requirement->name }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($dependents->isNotEmpty())
                    <h2 class="text-xl font-bold text-foreground-title mb-3">Que faire après ?</h2>
                    @foreach($dependents as $category => $technologies)
                            <h3 class="text-sm text-muted uppercase mb-2">{{ $category }}</h3>
                            <div class="flex flex-col divide-y divide-border border-y border-border overflow-hidden mb-6">
                                @foreach($technologies as $t)
                                    <a href="{{ route('technologies.show', $t) }}" class="hover:pl-2 py-2 hover:bg-list-hover transition-all">
                                        {{ $t->name }}
                                    </a>
                                @endforeach
                            </div>
                    @endforeach
            @endif
        </aside>

    </div>
@endsection
