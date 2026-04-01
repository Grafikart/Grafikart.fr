@props(['index', 'post'])

<article @class(["items-start flex relative gap-5 border-b pb-4 lg:pb-8 last:border-none last:pb-0", "lg:flex-col lg:border-none lg:pb-0!" => $index < 2, "lg:flex-row-reverse" => $index > 1])>
    <img @class(['size-22 rounded-sm', 'lg:hidden' => $index < 2]) src="{{ $post->attachment->url(160, 160) }}" width="160" height="160" alt="">
    @if($index === 0)
        <img class="rounded-sm hidden lg:block" src="{{ $post->attachment->url(370, 205) }}" alt="" width="370" height="205">
    @endif
    @if($index === 1)
        <img class="rounded-sm hidden lg:block object-cover" src="{{ $post->attachment->url(360, 450) }}" alt="" width="370" height="550">
    @endif
    <div class="flex-1">
        <h3 class="font-bold text-lg">
            <a href="{{ app_url($post) }}" class="overlay hover:text-primary">
                {{ $post->title }}
            </a>
        </h3>
        <p class="text-muted text-sm my-2">
            <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                {{ $post->created_at->translatedFormat('j F Y') }}
            </time>
        </p>
        <p class="leading-relaxed">
            {{ \App\Helpers\MarkdownHelper::excerpt($post->content, 125) }}
        </p>
    </div>

</article>
