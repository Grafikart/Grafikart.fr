@props(['selected' => false])

<option
    class="{{ cn([
        "py-1 px-4 bg-card flex justify-between rounded-sm selected:bg-primary/5 selected:text-primary hover:bg-background",
        $attributes->get('class')
    ]) }}"
    @selected($selected)
    {{ $attributes->except('class') }}
>
    {{$slot}}
</option>
