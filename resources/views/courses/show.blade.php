@extends('front')

@section('title', sprintf('Tutoriel video %s : %s',$course->mainTechnologies->pluck('name')->join(' & '), $course->title ))

@section('meta')
    <meta property="og:image" content="{{ url($course->youtubeThumbnail) }}"/>
    <meta property="og:created_time" content="{{ $course->created_at->toIso8601String() }}"/>
    <meta property="og:type" content="video.other"/>
    <meta property="og:duration" content="{{ $course->duration }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
@endsection

@section('body')

    <div class="grid md:grid-cols-[1fr_350px] container mx-auto py-6 gap-12">

        <main>
            <h1 class="text-4xl font-bold mb-2 font-serif">
                <span class="hidden">
                    @if($course->formation)
                        Formation {{ $course->formation->title }} :
                    @else
                        Tutoriel {{ $course->mainTechnologies->pluck('name')->join(' & ') }} :
                    @endif
                </span>
                {{ $course->title }}
            </h1>

            <div class="flex gap-8 mb-8">
                <div class="flex items-center text-muted gap-2">
                    <x-lucide-clock class="size-4"/>
                    <x-atoms.duration :duration="$course->duration"/>
                </div>
                <div class="flex items-center text-muted gap-2">
                    <x-lucide-graduation-cap class="size-4"/> {{ $course->level->name }}
                </div>
                <div class="flex items-center text-muted gap-2">
                    <x-lucide-tags class="size-4"/>
                    <div>
                        @foreach($course->technologies as $k => $technology)
                            <a href="#">
                                {{ $technology->name }}
                                @if($technology->pivot->version)
                                    <span class="text-sm opacity-70">({{$technology->pivot->version}})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center text-muted gap-2 ml-auto">
                    <x-lucide-calendar class="size-4"/> {{ $course->created_at->diffForHumans() }}
                </div>
            </div>

            <iframe
                class="aspect-video w-full mb-8 rounded-md shadow-lg"
                style="background: #000;"
                src="https://www.youtube-nocookie.com/embed/xSfZwqzs_OM?si=mcYzRO1_nis2Rort"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen></iframe>

            <div class="prose prose-lg">
                {!! \App\Helpers\MarkdownHelper::html($course->content) !!}
            </div>

        </main>

        <aside class="space-y-8">
            <x-atoms.card class="p-4 space-y-2 border">
                <div class="text-sm uppercase text-muted">Fichiers attachés</div>
                <x-atoms.button variant="outline" class="w-full">
                    <x-lucide-download class="text-muted"/>
                    Sources du projet
                </x-atoms.button>
                <x-atoms.button variant="secondary" class="w-full">
                    <x-lucide-video class="text-muted"/>
                    Télécharger la vidéo
                </x-atoms.button>
            </x-atoms.card>

            @if($course->formation)
                <x-organisms.chapters :chapters="$course->formation->chaptersWithCourses" :active="$course->id"/>
            @endif

            <section class="space-y-3">
                <h2 class="font-serif font-bold text-2xl flex items-center gap-2">
                    <x-lucide-circle-question-mark class="size-5"/>
                    Poser une question
                </h2>

                <x-atoms.card padded class="space-y-3">
                    <div class="space-y-1">
                       <textarea
                           aria-label="Description du problème"
                           class="p-2 border-border border w-full rounded-sm"
                           placeholder="Je ne comprends pas pourquoi tu as fait... à ce moment là"></textarea>
                    </div>

                    <x-atoms.button class="w-full justify-center">Poser ma question
                        <span class="opacity-50 text-sm inline-flex items-center">
                            (<x-lucide-clock class="size-3! mr-1"/> 10:30)
                        </span>
                    </x-atoms.button>
                </x-atoms.card>
            </section>

        </aside>
    </div>

@endsection
