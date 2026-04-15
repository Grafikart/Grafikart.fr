@extends('front', $course->formation_id ? ['style' => '--drawer-width: 350px', 'drawer' => 'right'] : [])

@section('title', sprintf('%s, Tutoriel video %s', $course->title, $course->mainTechnologies->pluck('name')->join(' & ')))

@section('head')
    <meta property="og:image" content="{{ $course->youtubeThumbnail }}"/>
    <meta property="og:created_time" content="{{ $course->created_at->toIso8601String() }}"/>
    <meta property="og:type" content="video.other"/>
    <meta property="og:duration" content="{{ $course->duration }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="video:start" content="{{ $start }}"/>
    <meta name="user:completed" content="{{ $completed->join(',') }}"/>
@endsection

@section('body')
    @cache("course-show", $course, ($course->isPublic() || $user?->isPremium()) ? 'visible' : 'premium')
    <main class="bg-background-light">
        <div class="max-w-container mx-auto space-y-2">
            <x-breadcrumbs :model="$course" />
            <h1 class="text-5xl font-bold font-serif text-foreground-title">
                <span class="hidden">
                    @if($course->formation)
                        Formation {{ $course->formation->title }} :
                    @else
                        Tutoriel {{ $course->mainTechnologies->pluck('name')->join(' & ') }} :
                    @endif
                </span>
                {{ $course->title }}
            </h1>

            <div class="flex gap-8">
                <div class="flex items-center text-muted gap-2">
                    <x-lucide-clock class="size-4"/>
                    {{ duration($course->duration) }}
                </div>
                <a href="{{ route('courses.index', ['level' => $course->level->value]) }}" class="flex items-center text-muted gap-2">
                    <x-lucide-graduation-cap class="size-4"/> {{ $course->level->name }}
                </a>
                <div class="flex items-center text-muted gap-2">
                    <x-lucide-calendar class="size-4"/>
                    <x-atoms.ago :date="$course->created_at"/>
                </div>
                <div class="flex justify-end gap-4 ml-auto">
                    @if($course->source)
                        <x-atoms.button download="{{ $course->filename() }}" href="{{ route('courses.download', ['course' => $course->id, 'type' => 'source']) }}" variant="secondary">
                            <x-lucide-download class="text-muted"/>
                            Sources du projet
                            @if($course->source_size)
                                <span class="text-xs text-muted text-trim mt-0.5">
                                        {{ file_size($course->source_size) }}
                                    </span>
                            @endif
                        </x-atoms.button>
                    @endif
                    <x-atoms.button download="{{ $course->filename() }}" variant="secondary" href="{{ route('courses.download', ['course' => $course->id, 'type' => 'video']) }}">
                        <x-lucide-video class="text-muted"/>
                        Télécharger la vidéo
                        @if($course->video_size)
                            <span class="text-xs text-muted text-trim mt-0.5">
                                    {{ file_size($course->video_size) }}
                                </span>
                        @endif
                    </x-atoms.button>
                </div>
            </div>

            @if($course->isPublic() || $user?->can('watch', $course))
                <x-atoms.course-video :course="$course->id" :video="$course->youtube_id ?? $course->video_url"
                                      :poster="$course->posterUrl(1330, 750)" class="mt-6 mb-12"/>
            @else
                <div class="aspect-video stack bg-cover relative rounded-lg mt-6 mb-12 overflow-hidden shadow-lg"
                     style="background-image: url({{ $course->posterUrl(1330, 750)  }})">
                    <div class="inset-0 absolute bg-video/80"></div>
                    <div class="relative z-2 text-white flex flex-col items-center gap-4">
                        <p class="mb1 text-3xl font-bold">
                            @if($course->isScheduled())
                                <span class="font-light">Disponible <x-atoms.ago class="font-bold"
                                                                                 :date="$course->created_at"/></span>
                            @else
                                Contenu destiné aux membres premiums
                            @endif
                        </p>
                        <x-atoms.button href="{{ route('premium') }}">
                            <x-lucide-star class="size-4"/>
                            Devenir premium
                        </x-atoms.button>
                    </div>
                </div>
            @endif
        </div>

        @php
            $hasEvaluation = $course->questions()->exists();
        @endphp

        <x-atoms.tabs as="nav-tabs">
            <x-atoms.tab href="#content" :active="true">
                <x-lucide-newspaper class="size-5"/>
                Résumé
            </x-atoms.tab>
            <x-atoms.tab href="#support">
                <x-lucide-circle-question-mark class="size-5"/>
                Support
            </x-atoms.tab>
            @if($hasEvaluation)
                <x-atoms.tab href="#quizz" :class="$quizCompleted ? 'text-success' : null">
                    @if($quizCompleted)
                        <x-lucide-thumbs-up class="size-5"/>
                    @else
                        <x-lucide-list-checks class="size-5"/>
                    @endif
                    Quiz
                </x-atoms.tab>
            @endif
        </x-atoms.tabs>

        <div class="bg-background pt-10 border-t pb-20">
            <div class="prose prose-lg max-w-200 mx-auto px-4" id="content">
                {!! \App\Helpers\MarkdownHelper::html($course->content) !!}
            </div>
            <div id="support" class="container" hidden>
                <support-course course="{{ $course->id }}"></support-course>
            </div>
            @if($hasEvaluation)
                <div id="quizz" class="max-w-200 mx-auto px-4" hidden>
                    <evaluation-questions
                        course="{{ $course->id }}"
                    ></evaluation-questions>
                </div>
            @endif
        </div>

    </main>

    @if($course->formation)
        <x-molecules.drawer side="right">
            <x-organisms.chapters :chapters="$course->formation->chaptersWithCourses" :active="$course->id"/>
        </x-molecules.drawer>
    @endif

    <x-molecules.revision-link :model="$course" class="mb-20"/>
    @endcache

    @can('edit', $course)
        <x-atoms.floating-button href="{{ route('cms.courses.edit', $course->id) }}">
            <x-lucide-edit/>
            Editer
        </x-atoms.floating-button>
    @endcan

@endsection
