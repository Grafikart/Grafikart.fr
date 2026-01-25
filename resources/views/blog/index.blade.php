@extends('front', ['class' => 'bg-background-light'])

@section('body')

    <header class="container flex items-center pb-10 justify-between">

        <h1 class="text-6xl font-serif font-bold">Blog</h1>
        <form method="GET" action="{{ route('blog.index') }}">
            <select
                name="category"
                onchange="this.form.submit()"
                class="border border-border rounded-lg px-4 py-3 pr-10 bg-card appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/20"
            >
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option
                        value="{{ $category->slug }}"
                        @selected($currentCategory === $category->slug)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </form>

    </header>

    <section class="bg-background border-t">
        <div class="container max-w-182 py-20 flex flex-col gap-20">

            @foreach($posts as $post)
                <article class="flex gap-8">
                    @if($post->attachment)
                        <a href="{{ route('blog.show', [$post->slug]) }}" class="size-40 hover:ring hover:ring-primary flex-none rounded-md overflow-hidden hover:shadow-lg transition-all">
                        <img
                            src="{{ $post->attachment->url(160,160) }}"
                            alt="{{ $post->title }}"
                            class="size-40 object-cover shadow-sm"
                        /></a>
                    @endif

                    <div>
                        <h2 class="text-4xl font-bold mb-4">
                            <a class="hover:text-primary transition-colors" href="{{route('blog.show', [$post->slug])}}">{{ $post->title }}</a>
                        </h2>

                        <div class="flex items-center gap-4 text-muted text-sm uppercase mb-4 justify-between">
                            <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                                {{ $post->created_at->translatedFormat('j F Y') }}
                            </time>
                            <span> Auteur Grafikart</span>
                        </div>

                        <p class="text-foreground/80 leading-relaxed">
                            {{ \App\Infrastructure\Blade\Markdown::excerpt($post->content, 300) }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        <div>
            {{$posts->links()}}
        </div>

    </section>
@endsection
