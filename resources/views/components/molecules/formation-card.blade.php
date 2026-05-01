@props(['formation', 'progress' => 0])

@cache('formation-card', $formation)
<x-atoms.card
    padded
    as="article"
    @class(["flex flex-col h-full hover:shadow-md transition-shadow relative card-stacked", "outline-success/20 outline-4 border-3 border-success before:triangle-top-12 overflow-hidden before:border-top-success before:text-success" => $progress === 1000])
>
    @if($progress === 1000)
        <x-lucide-check class="size-5 text-white absolute top-1 right-1"/>
    @endif
    @if($progress > 0 && $progress < 1000)
        <div class="absolute bottom-9 left-0 right-0 h-1 bg-border"><div style="width: {{ $progress / 10 }}%;" class="bg-primary h-1"></div></div>
    @endif
    <div class="flex items-start justify-between mb-3">
        <div class="h-10 flex items-center gap-1">
            @foreach($formation->mainTechnologies as $tech)
                <a href="{{ app_url($tech) }}" class="relative z-2">
                    <img
                        width="40"
                        height="40"
                        src="{{ $tech->mediaUrl('image') }}"
                        alt="{{ $tech->name }}"
                        class="size-10 object-contain"
                    />
                </a>
            @endforeach
        </div>

        @if($formation->level)
            <x-atoms.level-badge :level="$formation->level"/>
        @endif
    </div>

    <h2 class="font-semibold text-md mb-2 line-clamp-2 text-foreground-title">
        <a href="{{ route('formations.show', $formation) }}"
           class="overlay hover:text-primary hover:before:ring hover:before:ring-primary before:rounded-md">
            {{ $formation->title }}
        </a>
    </h2>

    <p class="text-muted text-sm mb-4">
        {!! \App\Helpers\MarkdownHelper::excerpt($formation->content, 130) !!}
    </p>

    <div
        class="flex items-center text-muted text-sm mt-auto bg-card-footer border-t border-border/50 -mx-4 px-4 -mb-4 py-2 rounded-b-md">
        <x-lucide-list class="size-4 mr-1"/>
        {{ $formation->courses->count() }} chapitres
        <x-lucide-clock class="size-4 mr-1 ml-auto"/>
        {{ duration($formation->duration) }}
    </div>
</x-atoms.card>
@endcache
