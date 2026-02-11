@props(['padded' => false, 'as' => 'div', 'hidden' => null, 'class' => null])

<{{ $as }}
    class="{{ cn(['bg-card border shadow-xs rounded-lg', 'p-4' => $padded, $class]) }}"
    @if($hidden)
        hidden
    @endif
    {{ $attributes }}
>
    {{ $slot }}
</{{ $as }}>
