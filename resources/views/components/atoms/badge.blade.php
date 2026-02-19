@props(['variant' => 'info'])

@php
    $classes = cn([
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
        'bg-success/10 text-success' => $variant === 'success',
        'bg-primary/10 text-primary' => $variant === 'info',
        'bg-destructive/10 text-destructive' => $variant === 'destructive',
        $attributes->get('class'),
    ]);
@endphp

<span {{ $attributes->except('class') }} class="{{ $classes }}">{{ $slot }}</span>
