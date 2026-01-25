@props(['course'])

@php
    $primaryTech = $course->technologies->first();
@endphp

<x-atoms.card
    padded
    as="article"
    class="flex flex-col rounded-md border h-full hover:shadow-md transition-shadow relative"
>
    <div class="flex items-start justify-between mb-3">
        @if($primaryTech && $primaryTech->image)
            <img
                src="{{ $primaryTech->image }}"
                alt="{{ $primaryTech->name }}"
                class="size-10 object-contain"
            />
        @else
            <div class="size-10"></div>
        @endif

        <x-atoms.level-badge :level="$course->level"/>
    </div>

    <h2 class="font-bold text-lg mb-2 line-clamp-2">
        <a href="{{  route('courses.show', [$course->slug, $course])  }}"
           class="overlay">
            {{ $course->title }}
        </a>
    </h2>

    <p class="text-muted text-sm mb-4">
        {{ str($course->content)->stripTags()->limit(150) }}
    </p>

    <div
        class="flex items-center text-muted text-sm mt-auto bg-background border-t border-border/50 -mx-4 px-4 -mb-4 py-2">
        @if($course->formation)
            <x-lucide-list class="size-4 mr-1"/>
            {{ $course->formation->title }}
        @endif
        <x-lucide-clock class="size-4 mr-1 ml-auto"/>
        <x-atoms.duration :duration="$course->duration"/>
    </div>
</x-atoms.card>
