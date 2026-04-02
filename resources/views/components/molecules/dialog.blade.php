@props(['title' => null, 'class' => '', 'id' => ''])

<dialog
    {{ $attributes }} class="fixed inset-0 hidden open:grid place-items-center w-full h-full backdrop:hidden group duration-300 transition-discrete text-foreground z-100"
    id="{{$id}}">
    <button tabindex="-1" title="Fermer"
            class="fixed inset-0 bg-overlay duration-300 group-open:opacity-80 opacity-0 starting:opacity-0 z-1"></button>
    <div
        class="{{ cn(['bg-card relative z-3 rounded-xl max-w-[calc(100%-2rem)] max-h-[calc(100%-2rem)] w-340 h-fit p-4 opacity-0 translate-y-4 group-open:opacity-100 group-open:translate-y-0 starting:translate-y-4 starting:opacity-0', $class]) }}"
        id="{{ $id }}">
        @if($title)
            <div class="gap-2 flex flex-col">
                <h2 class="text-xl leading-none font-bold">{{ $title }}</h2>
            </div>
        @endif
        {{ $slot }}
        <button type="button" commandfor="{{ $id }}" command="close"
                class="absolute top-2 right-2 p-2 rounded-full hover:bg-border/30 transition-background [&_svg]:size-6">
            <x-lucide-x/>
            <span class="sr-only">Close</span>
        </button>
    </div>
</dialog>
