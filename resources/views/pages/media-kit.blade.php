@extends('front', ['appearance' => 'light'])

@section('title', $lang === 'fr' ? 'Media kit Grafikart' : 'Grafikart media kit')

@section('head')
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('body')
    <main class="container space-y-16 py-10 text-lg">

        {{-- Intro --}}
        <section class="grid gap-8 md:grid-cols-2 justify-center">

            <div>
                <h1 class="text-6xl md:text-7xl font-bold font-serif mb-4 text-foreground-title">Grafikart <span
                        class="text-primary">Media kit</span></h1>

                <div class="text-xl leading-normal mb-8 space-y-4">
                    @if($lang === 'fr')
                        <p>
                            Grafikart est une chaîne <strong class="text-foreground-title">YouTube</strong> francophone
                            de référence dans le domaine du développement web sur laquelle je propose, depuis
                            <strong class="text-foreground-title">2008</strong> des tutoriels gratuits et de qualité
                            couvrant un large éventail de technologies web (PHP, JavaScript, CSS, NodeJS...).
                        </p>
                    @else
                        <p>
                            Grafikart is a french <strong class="text-foreground-title">YouTube</strong> channel
                            offering quality courses and videos about web development since
                            <strong class="text-foreground-title">2008</strong>.
                        </p>
                        <p>
                            The channel covers a wide range of web technologies (PHP, JavaScript, CSS, NodeJS...)
                        </p>
                    @endif
                </div>

                <div class="hidden md:grid gap-8 lg:grid-cols-2">
                    {{-- Demography --}}
                    <div>
                        <h2 class="text-2xl font-bold text-foreground-title whitespace-nowrap mb-4 flex items-center gap-2">
                            <x-lucide-users-round class="size-6"/>
                            {{ $lang === 'fr' ? 'Demographie' : 'Demography' }}
                        </h2>
                        <div class="text-2xl space-y-1">
                            <div><span class="text-primary font-bold">40%</span> France</div>
                            <div><span class="text-primary font-bold">85%</span> {{ $lang === 'fr' ? 'Homme' : 'Male' }}</div>
                            <div><span class="text-primary font-bold">40%</span> 18 - 24</div>
                        </div>
                    </div>

                    {{-- Brands --}}
                    <div class="flex flex-col">
                        <h2 class="text-2xl font-bold text-foreground-title whitespace-nowrap mb-4 flex items-center gap-2">
                            <x-lucide-target class="size-6"/>
                            {{ $lang === 'fr' ? 'Marques déjà présentes' : "Brands I've worked with" }}
                        </h2>
                        <div class="space-y-6">
                            <img src="/images/media-kit/logo_infomaniak_bleu.svg" alt="Infomaniak" class="w-50" width="278" height="37">
                            <img src="/images/media-kit/scaleway.svg" alt="Scaleway" class="w-50" width="166" height="32">
                            <img src="/images/media-kit/o2switch.svg" alt="o2switch" class="w-42.5" width="240" height="54">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <img src="/images/media-kit/logo-grafikart.svg" alt="Logo Grafikart"
                     class="rounded-full border-[5px] border-border size-50 mx-auto mb-4" width="500" height="500">
                <div class="space-y-6 md:w-max md:mx-auto">
                    <a href="https://www.youtube.com/@grafikart" class="flex gap-2 items-center">
                        <x-lucide-youtube class="size-12 text-muted" stroke-width="1.5"/>
                        <div class="leading-none">
                            <div class="text-3xl font-bold text-foreground-title">340 000</div>
                            <div>{{ $lang === 'fr' ? 'Abonnés' : 'Subscribers' }}</div>
                        </div>
                    </a>
                    <a href="https://www.twitch.tv/grafikart" class="flex gap-2 items-center">
                        <x-lucide-twitch class="size-12 text-muted" stroke-width="1.5"/>
                        <div class="leading-none">
                            <div class="text-3xl font-bold text-foreground-title">14 970</div>
                            <div>{{ $lang === 'fr' ? 'Suivis' : 'Followers' }}</div>
                        </div>
                    </a>
                    <a href="https://twitter.com/grafikart_fr" class="flex gap-2 items-center">
                        <x-lucide-twitter class="size-12 text-muted" stroke-width="1.5"/>
                        <div class="leading-none">
                            <div class="text-3xl font-bold text-foreground-title">40 700</div>
                            <div>{{ $lang === 'fr' ? 'Suivis' : 'Follows' }}</div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <hr class="border-t border-border">

        {{-- YouTube --}}
        <section class="space-y-16">
            <div>
                <x-lucide-youtube class="size-20 mx-auto text-[#FF0033]" stroke-width="1.5"/>
                <h2 class="text-5xl font-bold text-foreground-title text-center mb-2">
                    {{ $lang === 'fr' ? 'Statistiques Youtube' : 'Youtube statistics' }}
                </h2>
                <p class="text-lg text-center underline">
                    <a href="https://www.youtube.com/grafikart" target="_blank">www.youtube.com/grafikart</a>
                </p>
            </div>

            {{-- stats --}}
            <div class="grid gap-16 items-center md:grid-cols-2 w-max max-w-full mx-auto">
                <img src="/images/media-kit/youtube-channel.jpg" class="w-full max-w-125 mx-auto"
                     width="1008" height="591" alt="">

                <div class="w-max space-y-4 text-xl leading-none">
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">340 000</div>
                        <div class="text-muted">{{ $lang === 'fr' ? 'Abonnés' : 'Subscribers' }}</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">220 000</div>
                        <div class="text-muted">{{ $lang === 'fr' ? 'Vues / mois' : 'Views per months' }}</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">17 000</div>
                        <div class="text-muted">{{ $lang === 'fr' ? 'Heures de visionnage' : 'Hours of watch time' }}</div>
                    </div>
                </div>
            </div>

            {{-- Overview --}}
            <div>
                <h2 class="text-3xl font-bold text-foreground-title text-center mb-4">
                    {{ $lang === 'fr' ? "Statistiques sur l'année " : 'Overview for ' }}
                    <span class="text-primary">2025</span>
                </h2>
                <img src="/images/media-kit/yt-year.webp" class="max-w-full mix-blend-multiply mx-auto"
                     width="1972" height="506" alt="Statistique sur les 365 derniers jours">
            </div>

            <div>
                <h2 class="text-3xl font-bold text-foreground-title text-center mb-2">Audience</h2>

                <div class="mb-6 space-y-4 max-w-150 mx-auto">
                    @if($lang === 'fr')
                        <p>
                            Mon audience est principalement composée d'hommes entre 18 et 35 ans qui cherchent à se
                            lancer dans le métier de développeur web ou à améliorer leurs compétences.
                        </p>
                        <p>
                            La communauté est composée de spectateurs fidèles, persévérant et avide de découvrir de
                            nouvelles choses pour améliorer leur pratique du développement.
                        </p>
                    @else
                        <p>
                            My audience is mostly composed of men between 18 and 35 years old who are looking to get
                            into the web development or trying to improve their skills.
                        </p>
                        <p>
                            The community is composed of recurring viewers, invested and willing to discover new tools
                            to improve their software craftsmanship.
                        </p>
                    @endif
                </div>

                <img src="/images/media-kit/yt-audience.webp" alt="" class="w-full max-w-300 mx-auto mix-blend-multiply" width="1972" height="773">
            </div>
        </section>

        <hr class="border-t border-border">

        {{-- Twitch --}}
        <section class="space-y-16">
            <div>
                <x-lucide-twitch class="size-20 mx-auto text-[#a970ff]" stroke-width="1.5"/>
                <h2 class="text-5xl font-bold text-foreground-title text-center mb-2">
                    {{ $lang === 'fr' ? 'Statistiques Twitch' : 'Twitch statistics' }}
                </h2>
                <p class="text-lg text-center underline">
                    <a href="https://www.twitch.tv/grafikart" target="_blank">www.twitch.tv/grafikart</a>
                </p>
            </div>

            {{-- stats --}}
            <div class="grid gap-16 md:grid-cols-2 w-max max-w-full mx-auto">
                <img src="/images/media-kit/twitch.jpg" class="max-w-125 w-full mx-auto"
                     width="1008" height="591" alt="">

                <div class="w-max space-y-4 text-xl leading-none">
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">120</div>
                        <div>{{ $lang === 'fr' ? 'Spectateurs en moyenne' : 'Average viewers' }}</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">40h</div>
                        <div>{{ $lang === 'fr' ? 'par mois' : 'per month' }}</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-foreground-title">14 970</div>
                        <div>{{ $lang === 'fr' ? 'Suivis' : 'Followers' }}</div>
                    </div>
                </div>
            </div>

            {{-- Overview --}}
            <div>
                <h2 class="text-3xl font-bold text-foreground-title text-center mb-4">
                    {{ $lang === 'fr' ? 'Statistiques sur un ' : 'Overview of a ' }}
                    <span class="text-primary">{{ $lang === 'fr' ? 'mois' : 'month' }}</span>
                </h2>
                <img src="/images/media-kit/twitch-stats.webp" class="mix-blend-multiply w-full max-w-250 mx-auto"
                     width="1695" height="673" alt="Statistiques twitch sur le mois d'octobre">
            </div>
        </section>

        <hr class="border-t border-border">

        {{-- Services --}}
        <section>
            <x-lucide-handshake class="size-20 mx-auto text-muted" stroke-width="1.5"/>
            <h2 class="text-5xl font-bold text-foreground-title text-center mb-4">
                {{ $lang === 'fr' ? 'Services & tarifs' : 'Rates & Services' }}
            </h2>

            <h2 class="text-3xl font-bold text-foreground-title text-center mb-4">
                {{ $lang === 'fr' ? 'Offres / Services' : 'Services' }}
            </h2>
            <ul class="list-disc text-xl w-max mx-auto space-y-1">
                <li>{{ $lang === 'fr' ? "Vidéo personnalisée montrant l'utilisation du produit" : 'Custom video showcasing products' }}</li>
                <li>{{ $lang === 'fr' ? 'Intégration produit dans une vidéo relative' : 'Product integration on related videos' }}</li>
                <li>{{ $lang === 'fr' ? 'Mention de la marque ou du produit' : 'Brand mention or shoutout' }}</li>
                <li>{{ $lang === 'fr' ? 'Affiliation ou mise en avant de code de réduction personnalisé' : 'Affiliate link or custom discount code' }}</li>
            </ul>

            <img alt="" src="/images/media-kit/youtube-channel.jpg" class="w-full max-w-[700px] mx-auto my-16"
                 width="1008" height="591">

            <div class="grid lg:grid-cols-2 mt-10 gap-10">
                <div>
                    <h2 class="text-3xl font-bold text-foreground-title mb-2">
                        {{ $lang === 'fr' ? 'Tarifs pour YouTube' : 'Rates for YouTube' }}
                    </h2>
                    <ul class="divide-y divide-border text-xl">
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? "Vidéo personnalisée montrant l'utilisation du produit" : 'Custom video showcasing products' }}</span>
                            <span class="w-max flex-none">2 500 €</span>
                        </li>
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? 'Mention de la marque ou du produit' : 'Brand mention or shoutout' }}</span>
                            <span class="w-max flex-none">1 000 €</span>
                        </li>
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? 'Affiliation ou mise en avant de code de réduction personnalisé' : 'Affiliate link or custom discount code' }}</span>
                            <span class="w-max flex-none">1 000 €</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-foreground-title mb-2">
                        {{ $lang === 'fr' ? 'Tarifs pour Twitch' : 'Rates for Twitch' }}
                    </h2>
                    <ul class="divide-y divide-border text-xl">
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? "Découvert en live du produit / de l'outil" : 'Livecoding session to discover the product' }}</span>
                            <span class="w-max flex-none">400 €</span>
                        </li>
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? 'Logo en surimpression' : 'Brand visibility (scrolling logo)' }}</span>
                            <span class="w-max flex-none">150 €</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-foreground-title mb-2">
                        {{ $lang === 'fr' ? 'Tarifs pour grafikart.fr' : 'Rates for grafikart.fr' }}
                    </h2>
                    <ul class="divide-y divide-border text-xl">
                        <li class="flex justify-between py-4 gap-4">
                            <span>{{ $lang === 'fr' ? 'Lien naturel sur le blog' : 'Netlinking on the blog' }}</span>
                            <span class="w-max flex-none">300 €</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <hr class="border-t border-border">

        <a href="{{ route('contact') }}"
           class="flex justify-center w-full border border-primary text-primary font-bold py-2 px-4 rounded items-center gap-2 hover:bg-primary hover:text-primary-foreground transition-all">
            <x-lucide-mail class="size-5"/>
            {{ $lang === 'fr' ? 'Me contacter' : 'Contact me' }}
        </a>
    </main>
@endsection
