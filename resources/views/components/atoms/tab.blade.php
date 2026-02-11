@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    aria-controls="{{ str_replace('#', '', $href) }}"
    role="tab"
    @if($active)
        aria-selected="true"
    @endif
    {{ $attributes->merge(['class' => 'aria-selected:bg-background-light aria-selected:shadow-sm text-muted aria-selected:text-foreground px-1.5 py-0.5 rounded-sm font-medium hover:text-foreground flex items-center gap-2']) }}>
    {{ $slot }}
</a>
