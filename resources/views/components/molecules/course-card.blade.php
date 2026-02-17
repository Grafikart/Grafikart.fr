@props(['course'])

<x-atoms.card
    padded
    as="article"
    @class(["flex flex-col h-full hover:shadow-md transition-shadow relative", "outline-yellow/20 outline-5 border-yellow" => $course->isPremium()])
>
    @cache('course-card', $course, ($course->isScheduled() && !$user?->premium) ? 'scheduled' : 'public')
    <div class="flex items-start mb-3">
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

        <x-atoms.level-badge :level="$course->level" class="ml-auto"/>
        @if($course->premium)
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow/5 text-yellow flex items-center gap-1">
                <x-lucide-star class="size-3"/>
                Premium
            </span>
        @endif
    </div>

    <h2 class="font-semibold text-md mb-2 line-clamp-2 text-foreground-title">
        <a href="{{ route('courses.show', [$course->slug, $course])  }}"
           class="overlay hover:text-primary hover:before:ring hover:before:ring-primary before:rounded-md">
            {{ $course->title }}
        </a>
    </h2>

    <div class="stack">
        <p class="text-muted text-sm mb-4 {{ $course->isScheduled() ? 'opacity-10 blur-xs' : '' }}">
            {{ \App\Helpers\MarkdownHelper::excerpt($course->content, 130) }}
        </p>
        @if($course->isScheduled())
            <div class="flex flex-col items-center my-4 mb-8">
                <div class="text-muted text-sm">Disponible</div>
                <strong class="block mb-4">
                    <x-atoms.ago :date=" $course->created_at "/>
                </strong>
                <x-atoms.button href="{{ route('premium') }}" class="btn-primary btn-small relative">
                    <x-lucide-star/>
                    ou devenez premium
                </x-atoms.button>
            </div>
        @endif
    </div>

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
    @endcache
</x-atoms.card>
