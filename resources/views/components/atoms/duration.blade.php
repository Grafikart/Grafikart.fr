@props(['duration'])

@php
    $hours = floor($duration / 3600);
    $minutes = floor(($duration % 3600) / 60);
@endphp

<span {{ $attributes }}>
    @if ($hours > 0)
        {{ $hours }}h{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }}
    @else
        {{ $minutes }}min
    @endif
</span>
