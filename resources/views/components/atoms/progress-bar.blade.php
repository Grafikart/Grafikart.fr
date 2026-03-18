@props([
    'label' => 'Ma progression',
    'current' => 1,
    'total' => 58,
])

@php
    $progress = $current / $total;
@endphp

<div {{ $attributes }}>
    <div class="flex justify-between items-center mb-2">
        <p class="text-muted">
            {{ $label }}
        </p>
        <p>
            <span class="text-muted">Chapitre</span> <strong class="text-foreground-title">{{ $current }} / {{ $total }}</strong>
        </p>
    </div>

    <div class="rounded-full bg-white border p-1">
        <div class="relative h-3 overflow-hidden rounded-full bg-background">
            <div class="absolute inset-y-0 left-0 min-w-3 rounded-full bg-primary" style="width: {{ $progress * 100 }}%;"></div>
        </div>
    </div>
</div>
