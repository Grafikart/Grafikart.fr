@extends('front')

@section('title', $isLive ? 'Live en cours' : 'Prochain live')

@section('body')

    <div class="container">
        @if($isLive)
            <h1 class="text-page-title mb-8">Live en cours</h1>
        @else
            <h1 class="text-page-title mb-8">
                Prochain live<em class="text-muted text-lg font-normal font-sans">, {{ $liveAt->translatedFormat('l d F à H:i') }}</em>
            </h1>
        @endif

        <div class="rounded-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-[1fr_350px] items-stretch">
            <iframe
                src="https://player.twitch.tv/?channel=grafikart&parent={{ request()->getHost() }}"
                allowfullscreen
                class="w-full aspect-video"
            ></iframe>
            <iframe
                src="https://www.twitch.tv/embed/grafikart/chat?darkpopout&parent={{ request()->getHost() }}"
                class="w-full min-h-[350px]"
            ></iframe>
        </div>
    </div>

@endsection
