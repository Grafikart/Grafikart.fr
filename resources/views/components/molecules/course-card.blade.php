@props(['course'])

@cache('course-card', $course)
<x-atoms.card
    padded
    as="article"
    class="flex flex-col h-full hover:shadow-md transition-shadow relative"
>
    <div class="flex items-start justify-between mb-3">
        <div class="h-10 flex items-center gap-1">
            @foreach($course->mainTechnologies as $tech)
                <a href="{{ route('technologies.show', ['technology' => $tech->slug]) }}" class="relative z-2">
                    <img
                        src="{{ $tech->mediaUrl('image') }}"
                        alt="{{ $tech->name }}"
                        class="size-10 object-contain"
                    />
                </a>
            @endforeach
        </div>

        <x-atoms.level-badge :level="$course->level"/>
    </div>

    <h2 class="font-semibold text-md mb-2 line-clamp-2 text-foreground-title">
        <a href="{{ route('courses.show', [$course->slug, $course])  }}"
           class="overlay hover:text-primary hover:before:ring hover:before:ring-primary before:rounded-md">
            {{ $course->title }}
        </a>
    </h2>

    <p class="text-muted text-sm mb-4">
        {{ \App\Helpers\MarkdownHelper::excerpt($course->content, 130) }}
    </p>

    <div
        class="flex items-center text-muted text-sm mt-auto bg-card-footer border-t border-border/50 -mx-4 px-4 -mb-4 py-2 rounded-b-md">
        @if($course->formation)
            <x-lucide-list class="size-4 mr-1"/>
            <div class="overflow-hidden text-ellipsis line-clamp-1 mr-2"> {{ $course->formation->title }}
            </div>
        @endif
        <x-lucide-clock class="size-4 mr-1 ml-auto"/>
            {{ duration($course->duration) }}
    </div>
</x-atoms.card>
@endcache
