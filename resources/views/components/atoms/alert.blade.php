@props(['type'])

<x-atoms.card {{ $attributes->class([
'p-4 flex items-center border-l-3 gap-4',
'border-l-primary' => $type === 'info'
])}}>
    @if($type === 'info')
    <x-lucide-info class="text-primary size-5"/>
    @endif
    <p class="prose">
        {{ $slot  }}
    </p>
</x-atoms.card>
