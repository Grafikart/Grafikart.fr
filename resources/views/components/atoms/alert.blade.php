@props(['type', 'size' => 'default'])

<x-atoms.card {{ $attributes->class([
'flex items-center border-l-3',
'border-l-primary' => $type === 'info',
'p-4 gap-4' => $size === 'default',
'p-3 gap-2 text-sm' => $size === 'sm'
])}}>
    @if($type === 'info')
    <x-lucide-info @class(["text-primary flex-none", "size-5" => $size === 'default', 'size-4' => $size === 'sm'])/>
    @endif
    <p class="prose">
        {{ $slot  }}
    </p>
</x-atoms.card>
