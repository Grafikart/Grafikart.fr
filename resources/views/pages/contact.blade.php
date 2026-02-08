@extends('front')

@section('title', 'Me contacter')

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-4">Me contacter</h1>
        <p class="text-lg text-muted max-w-175">
            Si vous avez des problèmes ou un bug dans votre code n'utilisez pas ce formulaire, utilisez plutôt le
            système de support présent sur les vidéos.
        </p>
    </div>

    <div class="border-t bg-background pt-10">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-10">
                <form class="grid grid-cols-2 gap-4" method="post" action="{{ route('contact') }}">
                    <x-molecules.field name="name" required label="Votre nom"/>
                    <x-molecules.field name="email" required type="email" label="Votre email"/>
                    <x-molecules.field name="content" required label="Votre message" type="textarea"
                                       class="col-span-2"/>
                    <x-atoms.button>
                        <x-lucide-mail/>
                        Envoyer
                    </x-atoms.button>
                </form>
                <div class="space-y-5">
                    <div>
                        <h2 class="text-xl text-foreground-title font-bold mb-4">Sur les réseaux sociaux</h2>
                        @php
                            $socials = [
                                ['label' => 'YouTube', 'href' => 'https://www.youtube.com/user/grafikarttv', 'icon' => 'youtube'],
                                ['label' => 'GitHub', 'href' => 'https://github.com/Grafikart', 'icon' => 'github', 'class' => 'dark:brightness-1000'],
                                ['label' => 'Twitter', 'href' => 'https://twitter.com/grafikart_fr', 'icon' => 'twitter'],
                                ['label' => 'Twitch', 'href' => 'https://www.twitch.tv/grafikart', 'icon' => 'twitch'],
                            ];
                        @endphp
                        <div class="flex items-center">
                            @foreach($socials as $social)
                                <a href="{{ $social['href'] }}" title="{{ $social['label'] }}"
                                   class="hover:opacity-70 transition-opacity size-10 px-2">
                                    <img src="/images/icons/{{ $social['icon'] }}.svg" alt="{{ $social['label'] }}"
                                         width="24" height="24" loading="lazy" @class($social['class'] ?? '') />
                                </a>
                            @endforeach
                        </div>
                        <p class="prose text-muted">
                            Vous avez envie de parler en direct avec d'autres membres du site ? N'hésitez pas à faire un
                            tour sur le <a href="/tchat">serveur discord #grafikart</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
