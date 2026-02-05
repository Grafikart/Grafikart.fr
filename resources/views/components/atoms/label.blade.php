@props(['for' => null])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->class([
        'text-sm font-medium text-muted',
    ]) }}
>{{ $slot }}</label>
