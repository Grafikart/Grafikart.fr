@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'class' => null, 'inputClass' => null, 'help' => null, 'bag' => 'default'])

@php
    $label = $label ?? ucfirst($name);
@endphp

<div @class(['space-y-1', $class])>
    <div class="flex items-end justify-between">
        <x-atoms.label for="{{ $name }}">{{ $label }}</x-atoms.label>
        {{ $afterLabel ?? '' }}
    </div>
    @if($type === 'code')
        <code-input
            size="6"
            id="{{ $name }}"
            class="peer block"
            name="{{ $name }}"
            {{ $attributes->merge($errors->getBag($bag)->has($name) ? ['aria-invalid' => 'true'] : []) }}
        >{{ $slot }}</code-input>
    @else
    <x-atoms.input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        :value="old($name, $value)"
        :class="$inputClass"
        {{ $attributes->merge($errors->getBag($bag)->has($name) ? ['aria-invalid' => 'true'] : []) }}
    >{{ $slot }}</x-atoms.input>
    @endif
    @error($name, $bag)
        <p class="text-sm text-destructive hidden peer-aria-invalid:block">{{ $message }}</p>
    @enderror
    @if($help)
        <p class="text-sm">
            {!! $help !!}
        </p>
    @endif
</div>
