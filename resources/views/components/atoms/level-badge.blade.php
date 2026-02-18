@props(['level'])

@php
    $colors = [
        0 => 'bg-success/5 text-success',
        1 => 'bg-warning/5 text-warning',
        2 => 'bg-destructive/5 text-destructive',
    ];
@endphp

<span {{ $attributes->class([
    'px-2 py-0.5 rounded-full text-xs font-medium',
    $colors[$level->value] ?? 'bg-muted',
]) }}>
    {{ $level->label() }}
</span>
