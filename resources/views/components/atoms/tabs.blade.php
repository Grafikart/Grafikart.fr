@props(['as' => 'div', 'variant' => 'default'])

@php
    $classes = match ($variant) {
        'pill' => 'rounded-md bg-border/70 p-1 w-max flex items-center gap-1',
        default => 'container border-t bg-background/50 flex overflow-x-auto relative before:absolute before:left-0 before:right-0 before:bottom-0 before:h-px before:bg-border',
    };
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $as }}>
