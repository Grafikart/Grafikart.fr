@props(['checked' => false])

<div
    class="{{ cn([ 'relative', $attributes->get('class') ]) }}">
    <input
        type="checkbox"
        class="absolute inset-0 w-full h-full opacity-0 peer"
        @checked($checked)
        {{$attributes->except('class')}}
    />
    <div class="size-5 rounded-sm border bg-background peer-checked:bg-primary peer-checked:border-primary peer-checked:text-white grid place-items-center text-transparent peer-focus:shadow-focus peer-focus:border-primary">
        <x-lucide-check class="size-4"/>
    </div>
</div>
