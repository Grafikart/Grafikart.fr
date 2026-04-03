@props(['chapter', 'active' => false])

<a href="{{ route('courses.show', ['slug' => $chapter->slug, 'course' => $chapter->id])}}" class="{{ cn([
    'text-sm flex items-center gap-2 py-3 px-4  hover:bg-background border-y border-l-3 border-transparent',
    'border-primary bg-primary/10 text-primary' => $active,
    'not-premium:bg-warning-bg text-warning' => $chapter->premium,
    $attributes->get('class'),
]) }}" @if($active) aria-selected="true" @endif>
    @if($chapter->premium)
        <x-lucide-star class="size-4.5 -ml-0.5 -mt-0.5 premium:hidden text-warning"/>
    @endif
    <x-lucide-play @class(["size-4", "not-premium:hidden" => $chapter->premium, $active ? 'text-primary' : 'text-muted'])/>
    <p @class(["not-premium:text-warning" => $chapter->premium])>{{ $chapter->title }}</p>
    <span class="text-muted text-sm ml-auto">{{ duration($chapter->duration) }}</span>
</a>
