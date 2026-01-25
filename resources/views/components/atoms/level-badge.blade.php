@props(['level'])

@php
    $colors = [
        0 => 'bg-green/10 text-green',
        1 => 'bg-yellow/10 text-yellow',
        2 => 'bg-red/10 text-red',
    ];
@endphp

<span {{ $attributes->class([
    'px-2 py-0.5 rounded-full text-xs font-medium',
    $colors[$level->value] ?? 'bg-muted',
]) }}>
    {{ $level->label() }}
</span>
