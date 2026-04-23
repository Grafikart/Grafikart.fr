@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'as' => 'button'])

@php
    $cls = [
        'rounded-sm [&_svg]:size-4 flex justify-center items-center gap-3 transition-all w-max font-semibold ',
        // Variants
        'bg-primary text-primary-foreground hover:brightness-120 shadow-button ' => $variant === 'primary',
        'border hover:bg-border/30 bg-background font-medium' => $variant === 'secondary',
        'border border-primary text-primary hover:bg-primary/15' => $variant === 'outline',
        'bg-destructive text-destructive-foreground hover:brightness-140 ' => $variant === 'destructive',
        'hover:bg-border/30' => $variant === 'ghost',
        // Sizes
        'px-3 py-1 text-sm' => $size === 'sm',
        'px-4 py-2' => $size === 'md',
        'px-4 py-3 text-lg' => $size === 'lg',
        'p-2 rounded-full' => $size === 'icon',
        // Offset the icon a bit for better alignment
        '[&_svg:first-child]:-ml-0.5 [&_svg:last-child]:-mr-0.5' => $size !== 'icon',
    ];
@endphp

@if ($href)
    <a {{ $attributes->class($cls) }} href="{{ $href }}">{{ $slot }}</a>
@else
    <{{ $as }} {{ $attributes->class($cls) }}>{{ $slot }}</{{$as}}>
@endif
