@extends('front')

@section('title', 'Forum : ' . $topic->name )

@section('body')
    @cache('forum-topic', $topic)
    <div class="container pb-10 space-y-3 bg-background-light">
        <h1 class="text-4xl font-bold font-serif">
            {{ $topic->name }}
        </h1>

        <div class="flex gap-4">
            <p class="text-muted">
                Par <span class="text-foreground">{{ $topic->user?->name ?? 'Anonyme' }}</span>,
                <x-atoms.ago :date="$topic->created_at"/>
            </p>

            <x-atoms.separator orientation="vertical"/>

            @include('forum._tags')
        </div>
    </div>

    <main class="bg-background border-t pt-10 container space-y-10">
        <div class="prose top-30">
                {!! \App\Helpers\MarkdownHelper::htmlUntrusted($topic->content) !!}
            </div>

        <div>
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
                        <x-atoms.ago :date="$message->created_at"/>
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
