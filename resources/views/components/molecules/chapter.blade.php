@props(['chapter', 'active' => false])

<a href="{{ $chapter->url }}" class="{{ cn([
    'flex items-center justify-between gap-4 py-3 px-4 rounded-sm hover:bg-background border-transparent border-l-3',
    'bg-background border-primary' => $active,
    $attributes->get('class'),
]) }}">
    <p>{{ $chapter->title }}</p>
    <x-atoms.duration :duration="$chapter->duration" class="text-muted" />
</a>
