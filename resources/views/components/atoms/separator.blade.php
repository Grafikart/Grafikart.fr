@props(['orientation' => 'horizontal'])

<hr class="{{ cn([
    'bg-border',
    'h-px w-full' => $orientation === 'horizontal',
    'w-px' => $orientation === 'vertical',
    $attributes->get('class'),
]) }}" />
