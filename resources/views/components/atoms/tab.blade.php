@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    aria-controls="{{ $attributes->get('aria-controls', str_replace('#', '', $href)) }}"
    role="tab"
    aria-selected="{{ $active ? 'true' : 'false' }}"
    {{ $attributes->except('aria-controls')->merge(['class' => 'flex items-center gap-2 py-4 aria-selected:border-b-primary w-max aria-selected:text-primary px-4 relative border-b-2 border-b-transparent aria-selected:border-b-primary aria-selected:bg-list-hover hover:bg-list-hover hover:text-primary transition-all']) }}>
    {{ $slot }}
</a>
