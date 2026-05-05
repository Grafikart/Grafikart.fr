@extends('front')

@section('title', 'Recherche : ' . $q)

@section('body')
    <header class="bg-background-light container flex flex-col md:flex-row md:items-center pb-15 justify-between gap-8 md:gap-25">
        <h1 class="text-page-title w-max whitespace-nowrap text-trim">
            @if(empty($q))
                Ooops !
            @elseif(count($items) === 0)
                Aucun résultat
            @else
                {{ $total }} Résultat{{ $total > 1 ? 's' : '' }}
            @endif
        </h1>
        <form method="GET" action="{{ route('search.index') }}" class="relative w-full">
            <x-atoms.input type="text" name="q" class="pl-10" value="{{ $q }}" autofocus/>
            <button class="absolute left-3 top-1/2 size-5 -translate-y-1/2">
                <x-lucide-search class="size-4"/>
            </button>
        </form>
    </header>

    <section class="bg-background border-t pt-5">
        <div class="container">
            @if(empty($q))
                <div class="prose prose-lg text-center">
                    <p class="text-2xl">
                        Essayez de taper au moins un mot clé. Sinon je ne sais pas quoi vous trouver :(
                    </p>
                    <p>
                        Sérieusement je ne peux vraiment pas vous trouver ce que vous cherchez si vous ne m'aidez pas un
                        peu.<br/>
                        Alors je sais, parfois on arrive sur une nouvelle page et on ne sait plus ce que l'on cherche,
                        ça arrive !
                    </p>
                </div>
            @elseif(count($items) === 0)
                <p class="text-center text-muted text-3xl">
                    Aucun résultat ne semble correspondre à votre recherche :(
                </p>
            @else
                <ul class="divide-y max-w-182">
                    @foreach($items as $item)
                        <li class="py-8 relative">
                            <h2 class="font-bold text-xl text-foreground-title mb-1">
                                <a href="{{ $item->getUrl() }}" class="overlay hover:underline">{!! $item->getTitle() !!}</a>
                            </h2>
                            <p class="text-muted text-sm flex items-center gap-1 mb-2">
                                @if($item->getType() === 'Formation')
                                    <x-lucide-list class="size-4"/>
                                @elseif($item->getType() === 'Tutoriel')
                                    <x-lucide-video class="size-4"/>
                                @else
                                    <x-lucide-pen class="size-4"/>
                                @endif
                                {{ $item->getType() }}
                                @if(!empty($item->getCategories()))
                                    {{ implode(', ', $item->getCategories()) }}
                                @endif
                                | {{ $item->getCreatedAt()->diffForHumans() }}
                            </p>
                            <p class="mt-1">
                                {!! nl2br($item->getExcerpt()) !!}
                            </p>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-5">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
