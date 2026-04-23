@props(['side' => 'right'])

<aside id="drawer" {{ $attributes->class([
    'fixed top-(--header-height) bottom-0 bg-sidebar flex-col gap-6 pb-4 transition-position overflow-auto hidden lg:flex z-10',
    'right-0 border-l drawer-hidden:translate-x-full' => $side === 'right',
    'left-0 border-r drawer-hidden:-translate-x-full' => $side === 'left',
]) }}>
    {{ $slot }}
</aside>
