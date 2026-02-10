@props(['side' => 'right', 'class' => ''])

<aside {{ $attributes->merge(['class' => cn([
    'fixed top-(--header-height) bottom-0 w-88 bg-sidebar flex flex-col gap-6 pb-4 transition-all overflow-auto',
    'right-0 border-l' => $side === 'right',
    'left-0 border-r' => $side === 'left',
    $class
])]) }}>
    {{ $slot }}
</aside>
