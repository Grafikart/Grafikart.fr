@props(['post'])

@cache('post-card', $post)
<article class="flex flex-col gap-4 sm:flex-row sm:gap-8 min-w-0">
    @if($post->attachment)
        <a href="{{ route('blog.show', [$post->slug]) }}"
           class="hidden sm:block size-40 border hover:ring hover:ring-primary flex-none rounded-md overflow-hidden hover:shadow-lg transition-all">
            <img
                src="{{ $post->attachment->url(160,160) }}"
                alt="{{ $post->title }}"
                class="size-40 object-cover shadow-sm"
            />
        </a>
    @endif

    <div>
        <h2 class="text-4xl font-bold mb-4 text-foreground-title text-balance">
            <a class="hover:text-primary transition-colors"
               href="{{route('blog.show', [$post->slug])}}">{{ $post->title }}</a>
        </h2>

        <div class="flex items-center gap-4 text-muted text-sm uppercase mb-4 justify-between">
            <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                {{ $post->created_at->translatedFormat('j F Y') }}
            </time>
            <span> Auteur Grafikart</span>
        </div>

        <p class="leading-relaxed">
            {{ \App\Helpers\MarkdownHelper::excerpt($post->content, 300) }}
        </p>
    </div>
</article>
@endcache
