@props(['orientation' => 'horizontal', 'class'])

<hr class="{{ cn([
    'bg-border',
    'h-px w-full' => $orientation === 'horizontal',
    'w-px h-full' => $orientation === 'vertical',
    $attributes->get('class'),
]) }}" />
