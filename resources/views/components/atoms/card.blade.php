@props(['padded' => false, 'as' => 'div'])

<{{ $as }} class="{{ cn(['bg-card border shadow-xs rounded-lg', 'p-4' => $padded, $attributes->get('class')]) }}">
    {{ $slot }}
</{{ $as }}>
