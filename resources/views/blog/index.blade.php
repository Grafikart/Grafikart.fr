@extends('front')

@php
    $title = $category ? $category->name : 'Blog';
    $titleWithPage = $title;
    if ($page > 1) {
        $titleWithPage.= ', page ' . $page;
    }
@endphp

@section('title', $titleWithPage)

@section('body')

    <header class="container bg-background-light flex items-center pb-10 justify-between">

        <h1 class="text-page-title">
            {{ $title }}
            @if($page > 1)
                <span class="font-normal text-4xl -ml-2 text-muted">, page {{ $page }}</span>
            @endif
        </h1>
        <form method="GET" action="{{ route('blog.index') }}">
            <x-atoms.select
                name="category"
                onchange="window.location.href = '{{ route('blog.index') }}/category/' + this.value"
                class="dropdown group"
            >
                <x-atoms.button variant="secondary">
                    {{ $category ? $category->name : 'Toutes les catégories' }}
                    <x-lucide-chevron-down class="transition-all"/>
                </x-atoms.button>

                <x-atoms.option value="">Toutes les catégories</x-atoms.option>
                @foreach($categories as $c)
                    <x-atoms.option
                        value="{{ $c->slug }}"
                        :selected=" $category && $c->id === $category->id "
                    >
                        {{ $c->name }}

                        <div class="text-xs bg-border/50 rounded-full px-2 py-1">
                            {{$c->posts_count}}
                        </div>
                    </x-atoms.option>
                @endforeach
            </x-atoms.select>

        </form>

    </header>

    <section class="container bg-background border-t py-20">
        <div class="max-w-182 mx-auto flex flex-col gap-20">
            @foreach($posts as $post)
                <article class="flex gap-8">
                    @if($post->attachment)
                        <a href="{{ route('blog.show', [$post->slug]) }}"
                           class="size-40 hover:ring hover:ring-primary flex-none rounded-md overflow-hidden hover:shadow-lg transition-all">
                            <img
                                src="{{ $post->attachment->url(160,160) }}"
                                alt="{{ $post->title }}"
                                class="size-40 object-cover shadow-sm"
                            /></a>
                    @endif

                    <div>
                        <h2 class="text-4xl font-bold mb-4 text-foreground-title">
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
            @endforeach

            {{$posts->links()}}
        </div>


    </section>
@endsection
