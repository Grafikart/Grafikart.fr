@props(['user'])

@php
    $pill = 'inline-flex items-center px-1.5 rounded-sm text-sm text-trim';
@endphp

@if ($user->premium)
    <span {{ $attributes->class([$pill, 'gap-1 uppercase text-yellow bg-yellow/10']) }}>
        <x-lucide-star class="size-3" />
        premium
    </span>
@else
    <a href="{{ route('premium') }}" {{ $attributes->class([$pill, 'text-white bg-[#9fb3c8]']) }}>
        Compte standard
    </a>
@endif
