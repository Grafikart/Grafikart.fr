@props(['padded' => false, 'as' => 'div'])

<{{ $as }} class="{{ cn(['bg-card shadow-xs overflow-hidden rounded-2xl', 'p-4' => $padded, $attributes->get('class')]) }}">
    {{ $slot }}
</{{ $as }}>
