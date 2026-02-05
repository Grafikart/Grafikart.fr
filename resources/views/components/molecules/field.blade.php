@props(['name', 'label' => null, 'type' => 'text', 'value' => null])

@php
    $label = $label ?? ucfirst($name);
@endphp

<div class="space-y-1">
    <div class="flex items-end justify-between">
        <x-atoms.label for="{{ $name }}">{{ $label }}</x-atoms.label>
        {{ $afterLabel ?? '' }}
    </div>
    <x-atoms.input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        :value="old($name, $value)"
        {{ $attributes }}
    />
    @error($name)
        <p class="text-sm text-destructive">{{ $message }}</p>
    @enderror
</div>
