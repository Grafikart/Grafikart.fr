@props(['item'])

@php
    /** @var App\Http\Front\Data\StudentProgressData $item */
    $completed = $item->chapters === $item->completedChapters;
    $progress = round(($item->completedChapters / $item->chapters) * 100);
@endphp

<x-atoms.card
    as="article"
    @class(["flex flex-col h-full hover:shadow-md transition-shadow relative card-stacked",  "outline-primary/20 outline-4 border-3 border-primary" => $completed])
>
    <div class="p-4 space-y-4">
        <img class="size-10" src="{{ $item->icon }}" alt="">
        <h2 class="font-semibold text-md text-foreground-title">
            @if($item->url)
                <a href="{{ $item->url }}" class="overlay hover:text-primary">
                    {{ $item->title }}
                </a>
            @else
            {{$item->title}}
            @endif
        </h2>
    </div>
    <div class="h-1 bg-border">
        <div style="width: {{ $progress }}%;" class="bg-primary h-1"></div>
    </div>
    <div
        class="flex items-center text-muted text-sm mt-auto bg-card-footer px-4 py-2 rounded-b-md justify-between">
        <div class="flex items-center gap-2">
            <x-lucide-list class="size-4"/>
            {{$item->completedChapters}}/{{$item->chapters}}
            Chapitres
        </div>

        <div @class([
                            "flex items-center gap-2",
                            "text-primary font-bold" => $completed
                        ])>
            @if($completed)
                <x-lucide-check class="size-3"/>
                Terminé !
            @else
                <x-lucide-clock class="size-3"/> {{ $progress }}%
            @endif
        </div>
    </div>
</x-atoms.card>
