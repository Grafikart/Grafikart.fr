@extends('front', ['class' => 'bg-background-light'])

@section('title', 'Forum : ' . $topic->name )

@section('body')
    @cache('forum-topic', $topic)
    <div class="container pb-14 pt-5 space-y-3">
        <h1 class="text-4xl font-bold font-serif">
            {{ $topic->name }}
        </h1>

        <div class="flex gap-4">
            <p class="text-muted">
                Par <span class="text-foreground">{{ $topic->user?->name ?? 'Anonyme' }}</span>,
                {{ $topic->created_at->diffForHumans() }}
            </p>

            <x-atoms.separator orientation="vertical"/>

            @if($topic->tags->isNotEmpty())
                <div class="flex gap-2">
                    @foreach($topic->tags as $tag)
                        <div class="flex rounded-md overflow-hidden bg-card">
                            @if($tag->parent)
                                <span
                                    class="px-2"
                                    @if($tag->parent->color !== '#000000') style="background-color: {{ $tag->parent->color }}" @endif>
                                {{ $tag->parent->name }}
                            </span>
                            @endif
                            <a
                                class="pill px-2 small pill-square pill-grey"
                                @if($tag->color !== '#000000') style="background-color: {{ $tag->color }}" @endif>
                                {{ $tag->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <main class="bg-background border-t pt-10">

        <div class="container">

            <div class="prose  mb-10">
                {!! \App\Helpers\MarkdownHelper::htmlUntrusted($topic->content) !!}
            </div>


            <h2 class="text-4xl font-serif font-bold border-b pb-1">
                @php
                    $count = $topic->messages->count();
                @endphp
                @if($count === 0)
                    Aucune réponse
                @elseif($count === 1)
                    1 réponse
                @else
                    {{ $count }} réponses
                @endif
            </h2>

            <div class="flex flex-col gap-8 mt-4">
                @foreach($topic->messages as $message)
                    <article class="space-y-2">
                        <div>
                            {{ $message->user?->name ?? 'Anonyme' }},
                            <span class="text-muted text-sm">
                        {{ $message->created_at->diffForHumans() }}
                        </span>
                        </div>
                        <x-atoms.card class="prose p-4 rounded-md overflow-hidden">
                            {!! \App\Helpers\MarkdownHelper::htmlUntrusted($message->content) !!}
                        </x-atoms.card>
                    </article>
                @endforeach
            </div>
        </div>

    </main>

    @endcache

@endsection
