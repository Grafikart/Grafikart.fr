@extends('front')

@section('title', $post->title)

@section('head')
    @if($post->attachment)
    <meta property="og:image" content="{{ $post->attachment->url()  }}"/>
    @endif
    <meta property="og:created_time" content="{{ $post->created_at->toIso8601String() }}"/>
    <meta name="twitter:card" content="summary_large_image"/>
@endsection

@section('body')

    <div class="container max-w-182 py-20 flex flex-col">

        <h1 class="text-page-title mb-4">
            {{ $post->title }}
        </h1>
        <div class="flex items-center text-muted text-sm uppercase gap-2 justify-start mb-10">
                <span>
                    Posté le
                    <time datetime="{{ $post->created_at->format('Y-m-d') }}">
                        {{ $post->created_at->translatedFormat('j F Y') }}
                    </time>
                </span> -
            <a href="{{ route('blog.category', [$post->category->slug]) }}"
               class="flex items-center gap-1 hover:underline">
                <x-lucide-tag class="size-3"/> {{ $post->category->name }}
            </a>
            <span class="ml-auto"> Par Grafikart</span>
        </div>
        <div class="prose prose-lg">
            {!! \App\Helpers\MarkdownHelper::html($post->content) !!}
        </div>

    </div>

@endsection
