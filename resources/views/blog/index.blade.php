@extends('front', ['class' => 'bg-background-light'])

@section('body')

    <header class="container flex items-center pb-10 justify-between">

        <h1 class="text-5xl font-serif font-bold">Blog</h1>
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
        <div class="container max-w-182 py-10">

            @foreach($posts as $post)
                <article class="flex gap-8">
                    @if($post->attachment)
                        <div class="shrink-0">
                            <img
                                src="{{ $post->attachment->mediaUrl('name') }}"
                                alt="{{ $post->title }}"
                                class="w-40 h-32 object-cover rounded-md"
                            />
                        </div>
                    @endif

                    <div class="flex-1">
                        <h2 class="text-3xl font-bold text-primary mb-2 leading-tight">
                            {{ $post->title }}
                        </h2>

                        <div class="flex items-center gap-4 text-muted text-sm uppercase tracking-wide mb-4">
                            <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                                {{ $post->created_at->translatedFormat('j F Y') }}
                            </time>
                            <span class="ml-auto">Auteur Grafikart</span>
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
