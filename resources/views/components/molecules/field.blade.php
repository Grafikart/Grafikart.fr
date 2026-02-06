@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'class' => null])

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
        {{ $attributes->merge($errors->has($name) ? ['aria-invalid' => 'true'] : []) }}
    />
    @error($name)
        <p class="text-sm text-destructive hidden peer-aria-invalid:block">{{ $message }}</p>
    @enderror
</div>
