<x-atoms.card class="flex flex-col p-5 rounded-xl hover:shadow-lg relative transition group">
    <h2 class="text-xl leading-tight font-bold text-foreground-title mb-3">
        <a
            class="overlay hover:text-primary"
            href="{{ app_url($path) }}">
            {{ $path->title }}
        </a>
    </h2>
    <p class="mb-4 text-muted text-pretty">
        {{ $path->description }}
    </p>
    <div class="flex items-center flex-wrap gap-2">
        @foreach($path->tags() as $tag)
            <div
                class="bg-border/20 w-max rounded-xl p-2 py-1 uppercase text-xs text-muted group-hover:bg-primary/10 group-hover:text-primary">
                #{{ $tag }}
            </div>
        @endforeach
    </div>
</x-atoms.card>
