@props(['course'])

@cache('course-card', $course)
<x-atoms.card
    padded
    as="article"
    class="flex flex-col rounded-md border h-full hover:shadow-md transition-shadow relative"
>
    <div class="flex items-start justify-between mb-3">
        <div class="h-10 flex items-center gap-1">
            @foreach($course->mainTechnologies as $tech)
                <img
                    src="{{ $tech->mediaUrl('image') }}"
                    alt="{{ $tech->name }}"
                    class="size-10 object-contain"
                />
            @endforeach
        </div>

        <x-atoms.level-badge :level="$course->level"/>
    </div>

    <h2 class="font-bold text-lg mb-2 line-clamp-2 text-foreground-title">
        <a href="{{ route('courses.show', [$course->slug, $course])  }}"
           class="overlay hover:text-primary hover:before:ring hover:before:ring-primary before:rounded-md">
            {{ $course->title }}
        </a>
    </h2>

    <p class="text-muted text-sm mb-4">
        {{ \App\Infrastructure\Blade\Markdown::excerpt($course->content, 130) }}
    </p>

    <div
        class="flex items-center text-muted text-sm mt-auto bg-background border-t border-border/50 -mx-4 px-4 -mb-4 py-2 rounded-b-md">
        @if($course->formation)
            <x-lucide-list class="size-4 mr-1"/>
            {{ $course->formation->title }}
        @endif
        <x-lucide-clock class="size-4 mr-1 ml-auto"/>
        <x-atoms.duration :duration="$course->duration"/>
    </div>
</x-atoms.card>
@endcache
