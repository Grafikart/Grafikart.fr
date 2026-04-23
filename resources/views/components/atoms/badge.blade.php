@props(['variant' => 'info'])

<span {{ $attributes->class([
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
        'bg-success/10 text-success' => $variant === 'success',
        'bg-primary/10 text-primary' => $variant === 'info',
        'bg-destructive/10 text-destructive' => $variant === 'destructive',
    ]) }}>{{ $slot }}</span>
