<div class="group select-wrapper">
    <select
        class="select"
        {{ $attributes->except('class') }}
    >
        {{ $slot }}
    </select>
</div>
