@extends('front')

@php
    $title = 'Forum';
    $titleWithPage = $title;
    if ($page > 1) {
        $titleWithPage.= ', page ' . $page;
    }
@endphp

@section('title', $titleWithPage)

@section('body')
    @cache('forum-topic', $selectedTag->id, $page)
    <h1 class="container text-page-title pb-10 bg-background-light">
        {{ $title }}
        @if($page > 1)
            <span class="font-normal text-4xl -ml-2 text-muted">, page {{ $page }}</span>
        @endif
    </h1>

    <div class="container grid grid-cols-1 md:grid-cols-[200px_1fr] gap-6 bg-background border-t pb-20 pt-10">

        <x-atoms.alert type="info" class="col-span-full mb-4">
            Ce forum est en mode "lecture seule", il n'est plus possible de créer de nouveaux sujets. <a
                href="{{ route('forum.topic', ['topic' => 39578]) }}">En savoir plus...</a>
        </x-atoms.alert>

        <aside class="hidden md:block">
            <div class="forum-page__sidebar stack">
                <ul>
                    <li>
                        <a href="{{ route('forum.index') }}" class="py-1.5 hover:text-primary">
                            Tous les sujets
                        </a>
                    </li>
                    @foreach($tags as $tag)
                        <li>
                            <a href="{{ $tag->url() }}"
                               class="flex items-center gap-2 py-1.5 hover:text-primary aria-selected:text-primary aria-selected:bg-card aria-selected:border aria-selected:shadow-xs rounded-md px-2"
                               @if($selectedTag?->id === $tag->id) aria-selected="true" @endif>
                                <span class="block size-3 rounded-full"
                                      style="background-color:{{ $tag->color }};"></span>
                                {{ $tag->name }}
                            </a>
                            @if($tag->children->isNotEmpty())
                                <ul class="border-l ml-3 pl-4">
                                    @foreach($tag->children as $child)
                                        <li>
                                            <a href="{{ $child->url() }}"
                                               @if($selectedTag?->id === $child->id) aria-selected="true"
                                               @endif class="py-1 block hover:text-primary aria-selected:text-primary aria-selected:bg-card aria-selected:border aria-selected:shadow-xs rounded-md px-2">
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <main>
            <div class="-mt-4">
                @foreach($topics as $topic)
                    <div
                        class="p-4 hover:bg-card hover:shadow-xs rounded-md hover:border-border border border-transparent relative flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('forum.topic', $topic->id) }}"
                                   class="font-bold hover:underline overlay">
                                    {{ $topic->name }}
                                </a>
                            </div>
                            <div class="text-sm text-muted">
                                <x-atoms.ago :date="$topic->created_at"/> par {{ $topic->user?->name }}
                            </div>
                        </div>
                        @include('forum._tags', ['class' => 'text-xs flex-wrap max-w-min justify-end ml-auto min-w-40 text-muted mt-0.5'])
                        <div class="text-muted flex gap-1 items-center text-sm">
                            {{ $topic->messages_count }}
                            <x-lucide-messages-square class="size-4"/>
                        </div>
                    </div>
                @endforeach

                <div class="mt-8">
                    {{ $topics->links() }}
                </div>
            </div>
        </main>


    </div>
    @endcache
@endsection
