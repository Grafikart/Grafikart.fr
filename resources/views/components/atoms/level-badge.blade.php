@props(['level'])

@php
    $colors = [
        0 => 'bg-green/5 text-green',
        1 => 'bg-yellow/5 text-yellow',
        2 => 'bg-destructive/5 text-destructive',
    ];
@endphp

<span {{ $attributes->class([
    'px-2 py-0.5 rounded-full text-xs font-medium',
    $colors[$level->value] ?? 'bg-muted',
]) }}>
    {{ $level->label() }}
</span>
