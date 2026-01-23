@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
    $classes = cn([
        'rounded-md [&_svg]:size-4 flex items-center gap-2 transition-all',
        // Variants
        'bg-primary text-primary-foreground hover:brightness-140 ' => $variant === 'primary',
        'border border-border bg-background' => $variant === 'secondary',
        '' => $variant === 'destructive',
        '' => $variant === 'ghost',
        // Sizes
        '' => $size === 'sm',
        'px-4 py-2' => $size === 'md',
        '' => $size === 'lg',
        $attributes->get('class'),
    ]);
@endphp

@if ($href)
    <a {{ $attributes->except('class') }} class="{{ $classes }}">{{ $slot }}</a>
@else
    <button {{ $attributes->except('class') }} class="{{ $classes }}">{{ $slot }}</button>
@endif
