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
            <select
                name="category"
                onchange="window.location.href = '{{ route('blog.index') }}/category/' + this.value"
                class="select"
            >
                <x-atoms.button variant="secondary">
                    {{ $category ? $category->name : 'Toutes les catégories' }}
                    <x-lucide-chevron-down class="transition-transform"/>
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
            </select>

        </form>

    </header>

    <section class="container bg-background border-t py-20">
        <div class="max-w-182 mx-auto flex flex-col gap-20">
            @foreach($posts as $post)
                <x-molecules.post-card :post="$post"/>
            @endforeach

            {{$posts->links()}}
        </div>


    </section>
@endsection
