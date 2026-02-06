@props(['type' => 'text'])

@php
$cls = [
        'w-full rounded-sm border bg-background px-3 py-2 text-sm transition-colors peer',
        'placeholder:text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20',
        'disabled:cursor-not-allowed disabled:opacity-50',
        'aria-invalid:border-destructive aria-invalid:focus:ring-destructive/20'
    ];
@endphp

@if($type === 'textarea')
<textarea
    @if ($attributes->has('aria-invalid'))
        onchange="this.removeAttribute('aria-invalid')"
    @endif
    {{ $attributes->merge(['class' => cn([...$cls, 'h-37'])]) }}
></textarea>
@else
<input
    type="{{ $type }}"
    @if ($attributes->has('aria-invalid'))
        onchange="this.removeAttribute('aria-invalid')"
    @endif
    {{ $attributes->merge(['class' => cn([...$cls, 'h-10'])]) }}
/>
@endif
