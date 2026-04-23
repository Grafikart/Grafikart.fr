@props(['checked' => false, 'size' => 'default'])

<label @class([
        'container-size rounded-full border bg-background has-[:checked]:bg-primary has-[:focus]:shadow-focus has-[:focus]:border-primary duration-300 flex relative items-center px-px',
        'h-4.5 w-8' => $size === 'default',
        'h-3.5 w-6' => $size === 'sm',
        $attributes->get('class')
    ])>
    <input
        type="checkbox"
        class="sr-only peer"
        @checked($checked)
        {{ $attributes->except('class') }}
    />
    <span class="bg-card border rounded-full size-[calc(100cqh-2px)] peer-checked:translate-x-[calc(100cqw-100cqh+2px)] duration-300"></span>
</label>
