@extends('front', $course->formation_id ? ['style' => '--drawer-width: 350px', 'drawer' => 'right'] : [])

@section('title', sprintf('Tutoriel video %s : %s',$course->mainTechnologies->pluck('name')->join(' & '), $course->title ))

@section('head')
    <meta property="og:image" content="{{ $course->youtubeThumbnail }}"/>
    <meta property="og:created_time" content="{{ $course->created_at->toIso8601String() }}"/>
    <meta property="og:type" content="video.other"/>
    <meta property="og:duration" content="{{ $course->duration }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="video:start" content="{{ $course->startTimeForUser($user) }}"/>
@endsection

@section('body')
    @cache("course-show", ($course->isPublic() || $user?->isPremium()) ? 'visible' : 'premium')
    <main class="bg-background-light">
        <div class="max-w-container mx-auto space-y-2">
            <x-breadcrumbs :model="$course" class="hidden"/>
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
                    <x-lucide-tags class="size-4"/>
                    <div>
                        @foreach($course->technologies as $k => $technology)
                            <a href="#" class="hover:underline">
                                {{ $technology->name }}
                                @if($technology->pivot->version)
                                    <span class="text-sm opacity-70">({{$technology->pivot->version}})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                @if($course->formation)
                    <a href="{{ route('formations.show', $course->formation->slug) }}"
                       class="flex items-center text-muted gap-2 hover:underline">
                        <x-lucide-graduation-cap class="size-4"/>
                        {{ $course->formation->title }}
                    </a>
                @endif
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

        <div class="bg-background pt-10 border-t pb-20">
            <div class="prose prose-lg max-w-200 mx-auto px-4">
                {!! \App\Helpers\MarkdownHelper::html($course->content) !!}
            </div>
        </div>

    </main>

    @if($course->formation)
        <x-molecules.drawer side="right">
            <x-organisms.chapters :chapters="$course->formation->chaptersWithCourses" :active="$course->id"/>
            <div class="space-y-2 px-4 mt-auto">
                <div class="text-sm uppercase text-muted">Fichiers attachés</div>
            </div>
        </x-molecules.drawer>
    @endif

    @if($course->questions()->exists())
        <questions-button
            course="{{ $course->id }}"
            completed="{{ $quizCompleted ? 'true' : 'false' }}"
        ></questions-button>
    @endif

    @endcache

    <x-molecules.revision-link :model="$course"/>

@endsection
