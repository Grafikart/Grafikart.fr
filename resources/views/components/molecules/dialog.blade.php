@props(['title' => null, 'class' => ''])

<dialog {{ $attributes->merge(['class' => cn([
    'opacity-0 translate-y-4 open:opacity-100 open:translate-y-0 starting:open:opacity-0 starting:open:translate-y-4',
    'transition-[opacity,translate,display,overlay] duration-300 [transition-behavior:allow-discrete]',
    'backdrop:bg-overlay backdrop:backdrop-blur-xs backdrop:opacity-0 backdrop:transition-[opacity,display,overlay] backdrop:duration-300 backdrop:[transition-behavior:allow-discrete]',
    'open:backdrop:opacity-100 starting:open:backdrop:opacity-0',
    'bg-background ring-foreground/10 grid max-w-[calc(100%-2rem)] max-h-[calc(100%-2rem)] gap-4 rounded-xl p-4 text-sm ring-1 m-auto h-fit w-full outline-none',
    $class,
])]) }}>
    @if($title)
        <div class="gap-2 flex flex-col">
            <h2 class="text-xl leading-none font-bold">{{ $title }}</h2>
        </div>
    @endif

    {{ $slot }}

    <button type="button" onclick="this.closest('dialog').close()" class="absolute top-2 right-2 p-2 rounded-full hover:bg-border/30 transition-all [&_svg]:size-4">
        <x-lucide-x />
        <span class="sr-only">Close</span>
    </button>
</dialog>
