@aware(['variant' => 'default'])
@props(['href', 'active' => false, 'variant' => $variant])

@php
    $classes = match ($variant) {
        'pill' => 'aria-selected:bg-background-light aria-selected:shadow-sm text-muted aria-selected:text-foreground px-1.5 py-0.5 rounded-sm font-medium hover:text-foreground flex items-center gap-2',
        default => 'flex items-center gap-2 py-4 aria-selected:border-b-primary w-max aria-selected:text-primary px-4 relative border-b-2 border-b-transparent aria-selected:border-b-primary aria-selected:bg-list-hover hover:bg-list-hover hover:text-primary transition-all',
    };
@endphp

<a
    href="{{ $href }}"
    aria-controls="{{ $attributes->get('aria-controls', str_replace('#', '', $href)) }}"
    role="tab"
    aria-selected="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->except('aria-controls')->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
