@extends('front')

@section('body')

    <div class="grid grid-cols-[1fr_350px] container mx-auto py-6 gap-8">

        <div class="col-span-2">
            <h1 class="text-4xl font-bold mb-2 font-serif">{{ $course->title }}</h1>

            <div class="flex gap-8">
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
        </div>

        <main>
            <x-atoms.card as="main">
                <iframe
                        class="aspect-video w-full mb-8"
                        style="background: #000;"
                        src="https://www.youtube-nocookie.com/embed/xSfZwqzs_OM?si=mcYzRO1_nis2Rort"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen></iframe>

                <div class="prose prose-lg px-8">
                    {!! \App\Infrastructure\Blade\Markdown::html($course->content) !!}
                </div>

            </x-atoms.card>
        </main>

        <aside class="flex flex-col gap-4 space-y-4">

            <x-atoms.card padded class="hidden">
                <div class="px-8 py-4 flex flex-col justify-end gap-4">
                    <x-atoms.button variant="secondary">
                        <x-lucide-square-code/>
                        Télécharger les sources
                    </x-atoms.button>
                    <x-atoms.button variant="secondary">
                        <x-lucide-file-play/>
                        Télécharger la vidéo
                    </x-atoms.button>
                </div>
            </x-atoms.card>

            @if($course->formation)
                <x-organisms.chapters :chapters="$course->formation->chaptersWithCourses()"/>
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
