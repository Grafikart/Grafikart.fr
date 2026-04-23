@props(['selected' => false])

<option {{ $attributes->class([
        "py-1 px-4 bg-card flex justify-between rounded-sm selected:bg-primary/5 selected:text-primary hover:bg-background",
    ]) }}
    @selected($selected)
>
    {{$slot}}
</option>
