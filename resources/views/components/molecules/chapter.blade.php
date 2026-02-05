@props(['chapter', 'active' => false])

<a href="{{ $chapter->url }}" class="{{ cn([
    'text-sm flex items-center gap-2 py-3 px-4  hover:bg-background border-y border-l-3 border-transparent',
    'border-primary bg-primary/10 text-primary' => $active,
    $attributes->get('class'),
]) }}" @if($active) aria-selected="true" @endif>
    <x-lucide-play class="size-4 {{ $active ? 'text-primary' : 'text-muted' }}"/>
    <p>{{ $chapter->title }}</p>
    <x-atoms.duration :duration="$chapter->duration" class="text-muted text-sm ml-auto" />
</a>
