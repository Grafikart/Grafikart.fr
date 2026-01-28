@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
    $classes = cn([
        'rounded-sm [&_svg]:size-4 flex items-center gap-3 transition-all w-max',
        // Variants
        'bg-primary text-primary-foreground hover:brightness-140 ' => $variant === 'primary',
        'border hover:bg-border/30 bg-background-light' => $variant === 'secondary',
        'border hover:bg-border/30' => $variant === 'outline',
        'bg-destructive text-destructive-foreground hover:brightness-140 ' => $variant === 'destructive',
        'hover:bg-border/30' => $variant === 'ghost',
        // Sizes
        'px-3 py-1 text-sm' => $size === 'sm',
        'px-4 py-2' => $size === 'md',
        'px-4 py-3 text-lg' => $size === 'lg',
        'p-2 rounded-full' => $size === 'icon',
        // Offset the icon a bit for better alignment
        '[&_svg:first-child]:-ml-0.5 [&_svg:last-child]:-mr-0.5' => $size !== 'icon',
        $attributes->get('class'),
    ]);
@endphp

@if ($href)
    <a {{ $attributes->except('class') }} href="{{ $href }}" class="{{ $classes }}">{{ $slot }}</a>
@else
    <button {{ $attributes->except('class') }} class="{{ $classes }}">{{ $slot }}</button>
@endif
