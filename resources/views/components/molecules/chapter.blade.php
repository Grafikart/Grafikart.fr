@props(['chapter', 'active' => false])

<a href="{{ route('courses.show', ['slug' => $chapter->slug, 'course' => $chapter->id])}}#autoplay" {{ $attributes->class([
    'text-sm flex items-center gap-2 py-3 px-4  hover:bg-list-hover border-y border-l-3 border-transparent group',
    'border-primary bg-primary/10 text-primary' => $active,
    'not-premium:bg-warning-bg text-warning' => $chapter->premium,
]) }} is="has-completed" data-id="{{ $chapter->id }}" @if($active) aria-selected="true" @endif>
    @if($chapter->premium)
        <x-lucide-star class="size-6 text-warning group-data-completed:hidden"/>
    @else
        <x-lucide-play @class(["size-5 mx-1 group-data-completed:hidden", $active ? 'text-primary' : 'text-muted'])/>
    @endif
    <svg class="size-6 hidden group-data-completed:block" fill="none" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 32 32">
        <circle cx="16" cy="16" r="15" fill="var(--color-success)" stroke-width="1"></circle>
        <path d="M20.3 12.3L14 18.58l-2.3-2.3a1 1 0 00-1.4 1.42l3 3a1 1 0 001.4 0l7-7a1 1 0 00-1.4-1.42z"
              fill="#fff"></path>
    </svg>
    <p @class(["not-premium:text-warning" => $chapter->premium])>
        {{ $chapter->title }}<br/>
        <time-ago prefix="Disponible" hidden class="premium:hidden text-xs text-muted" display="future"
                  time="{{ $chapter->created_at->getTimestamp() }}"></time-ago>
    </p>
    <span class="text-muted text-sm ml-auto">{{ duration($chapter->duration) }}</span>
</a>
