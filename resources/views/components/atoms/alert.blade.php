@props(['type', 'size' => 'default'])

@php
$iconCls = [
    "flex-none",
    "size-5" => $size === 'default',
    'size-4' => $size === 'sm',
    'text-primary' => $type !== 'warning',
    'text-warning' => $type === 'warning'
]
@endphp

<x-atoms.card {{ $attributes->class([
'flex items-center border-l-3',
'border-l-primary' => $type === 'info',
'border-warning bg-warning/5 text-warning-text' => $type === 'warning',
'p-4 gap-4' => $size === 'default',
'p-3 gap-2 text-sm' => $size === 'sm'
])}}>
    @if($type === 'info')
        <x-lucide-info @class($iconCls)/>
    @elseif($type === 'warning')
        <x-lucide-triangle-alert @class($iconCls)/>
    @endif
    <p class="prose">
        {{ $slot  }}
    </p>
</x-atoms.card>
