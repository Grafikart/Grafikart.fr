@props(['orientation' => 'horizontal'])

<hr {{ $attributes->class([
    'bg-border',
    'h-px w-full' => $orientation === 'horizontal',
    'w-px' => $orientation === 'vertical',
]) }} />
