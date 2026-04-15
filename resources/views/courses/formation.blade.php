@extends('front')

@section('title', sprintf('Formation %s',$formation->title))

@section('head')
    @cache('formation-show-head', $formation)
    <meta property="og:image" content="{{ $formation->youtubeThumbnail }}"/>
    <meta property="og:created_time" content="{{ $formation->created_at->toIso8601String() }}"/>
    <meta property="og:type" content="video.other"/>
    <meta property="og:duration" content="{{ $formation->duration }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    @endcache
    <meta name="user:completed" content="{{ $completed->join(',') }}"/>
@endsection

@section('body')
    @php
        $technology = $formation->technology();
        $course = \App\Domains\Course\Course::query()->selectForUrl()->find($formation->chapters->first()->ids[0])
    @endphp

    <div class="bg-background-light pb-12">
        <div class="max-w-container mx-auto grid md:grid-cols-[1fr_400px] gap-8 items-center">
            <div class="space-y-4">
                <x-breadcrumbs :model="$formation"/>
                <h1 class="text-5xl font-bold font-serif text-foreground-title">
                    <span class="hidden">Formation</span> {{ $formation->title }}
                </h1>
                @if($formation->short)
                    <div class="prose prose-lg">
                        {!! \App\Helpers\MarkdownHelper::html($formation->short) !!}
                    </div>
                @endif

                @if($completed->count() > 0 && auth()->user())
                    <x-atoms.progress-bar :current="$completed->count()" :total="$total"/>
                    <x-atoms.button size="lg"
                                    href="{{ route('formations.continue', ['formation' => $formation->slug]) }}#autoplay">
                        <x-lucide-play-circle/>
                        Continuer
                    </x-atoms.button>
                @else

                <x-atoms.button size="lg" href="{{ app_url($course) }}">
                    <x-lucide-play-circle/>
                    Commencer
                </x-atoms.button>
                @endif
            </div>
            @if($technology)
                <img src="{{ $technology->mediaUrl('image') }}" alt="" class="max-w-60 mx-auto hidden md:block">
            @endif
        </div>
    </div>


    @cache('formation-show', $formation)
    <div class="container py-10 grid grid-cols-1 md:grid-cols-[1fr_420px] gap-30">
        {{-- Presentation --}}
        <div>
            <div class="sticky top-20">
                <h2 class="text-4xl text-foreground-title font-serif font-bold mb-4">Présentation</h2>


                @if($course && !$formation->hasYoutubeLink())
                    <x-atoms.course-video :course="$course->id" :video="$course->youtube_id ?? $course->video_url"
                                          :poster="$course->posterUrl(1330, 750)" class="mb-4"/>
                @endif

                <div class="prose prose-lg mb-8">
                    {!! \App\Helpers\MarkdownHelper::html($formation->content) !!}
                </div>
                @php
                    $hasPrerequisite = $formation->secondaryTechnologies->isNotEmpty();
                @endphp
                <div @class(["grid grid-cols-1 gap-6", $hasPrerequisite ? 'md:grid-cols-3' : 'md:grid-cols-2 max-w-170'])>
                    @if(!empty($formation->links))
                        <div class="bg-background-dark p-5 rounded-lg">
                            <div class="font-bold text-foreground-title mb-4">Liens utiles</div>
                            <div class="list-big">
                                {!! \App\Helpers\MarkdownHelper::html($formation->links) !!}
                            </div>
                        </div>
                    @endif

                    <div class="bg-background-dark p-5 rounded-lg">
                        <div class="font-bold text-foreground-title mb-4">Informations</div>
                        <ul class="list-big">
                            <li>{{ duration($formation->duration) }} de vidéos</li>
                            <li>{{ $formation->courses->count() }} chapitres</li>
                        </ul>
                    </div>

                    @if($hasPrerequisite)
                        <div class="bg-background-dark p-5 rounded-lg">
                            <div class="font-bold text-foreground-title mb-4">Prérequis</div>
                            <ul class="list-big">
                                @foreach($formation->secondaryTechnologies as $technology)
                                    <li>
                                        <a href="{{ route('technologies.show', $technology) }}">{{ $technology->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sommaire --}}
        <div class="space-y-4">
            <h2 class="hidden">Chapitres</h2>
            @foreach ($formation->chaptersWithCourses as $k => $chapter)
                <x-atoms.card class="overflow-hidden">
                    <div
                        class="p-4 py-2 bg-background/50 flex items-center gap-2 cursor-pointer border-b">
                        <div class="mr-auto">
                            <div class="text-muted text-sm">Chapitre {{$k + 1}}</div>
                            <h3 class="text-foreground-title font-semibold text-xl">{{ $chapter['title'] }}</h3>
                        </div>
                    </div>
                    @foreach ($chapter['courses'] as $index => $course)
                        <x-molecules.chapter
                            :chapter="$course"
                            :index="$index + 1"
                        />
                    @endforeach
                </x-atoms.card>
            @endforeach
        </div>
    </div>
    @endcache
    @can('edit', $formation)
        <x-atoms.floating-button href="{{ route('cms.formations.edit', $formation->id) }}">
            <x-lucide-edit/>
            Editer
        </x-atoms.floating-button>
    @endcan
@endsection
