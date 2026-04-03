@props(['as' => 'div'])

<{{ $as }} {{ $attributes->merge(['class' => 'container border-t bg-background/50 flex overflow-x-auto relative before:absolute before:left-0 before:right-0 before:bottom-0 before:h-px before:bg-border']) }}>
    {{ $slot }}
</{{ $as }}>
