@extends('front')

@section('title', 'Sponsors')

@section('body')

    {{-- Hero --}}
    <section class="bg-background-light container pb-12">
        <h1 class="text-page-title mb-4">Sponsors & Affiliation</h1>
        <div class="prose prose-lg">
            <p>
                Mon objectif avec Grafikart est de faire en sorte d'offrir un maximum de contenu accessible publiquement
                pour permettre à tout le monde d'apprendre le développement ou de s'améliorer.
                Cependant, c'est un travail considérable qui n'est rendu possible que par le support des utilisateurs et
                des sponsors.
            </p>
        </div>
    </section>

    {{-- Current sponsors --}}
    <section class="border-t bg-background py-12">
        <div class="container">
            <h2 class="text-2xl font-bold mb-2 text-foreground-title">Sponsors</h2>
            <p class="mb-8 text-lg">
                Dans un objectif de transparence, mais aussi pour les remercier, voici la liste des derniers sponsors du
                site.
            </p>
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($sponsors as $affiliate)
                    <div class="flex flex-col md:grid md:grid-cols-[150px_1fr] gap-4 items-center">
                        @if($affiliate->mediaUrl('logo'))
                            <a href="{{ $affiliate->url }}"
                               class="aspect-square border grid place-items-center p-2 bg-background-light rounded-sm hover:border-primary duration-300">
                                <img
                                    src="{{ $affiliate->mediaUrl('logo') }}"
                                    alt="{{ $affiliate->name }}"
                                    class="object-contain"
                                />

                            </a>
                        @endif
                        <div>
                            <h2 class="font-bold text-foreground-title text-xl mb-2">
                                <a href="{{ $affiliate->url }}" class="hover:text-primary">
                                    {{ $affiliate->name }}
                                </a>
                            </h2>
                            <div class="prose">
                                {!! \App\Helpers\MarkdownHelper::html($affiliate->content) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t bg-background py-12" id="affiliations">
        <div class="container">
            <h2 class="text-2xl font-bold mb-2 text-foreground-title">Affiliation</h2>
            <p class="mb-8 text-lg">
                Si vous souhaitez soutenir le site vous pouvez utiliser les liens partenaire pour souscrire ces
                différents produits.
            </p>
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($affiliates as $affiliate)
                    <div class="flex flex-col md:grid md:grid-cols-[150px_1fr] gap-4 items-center">
                        @if($affiliate->mediaUrl('logo'))
                            <a href="{{ $affiliate->url }}"
                               class="aspect-square border grid place-items-center p-2 bg-background-light rounded-sm hover:border-primary duration-300">
                                <img
                                    src="{{ $affiliate->mediaUrl('logo') }}"
                                    alt="{{ $affiliate->name }}"
                                    class="object-contain"
                                />

                            </a>
                        @endif
                        <div>
                            <h2 class="font-bold text-foreground-title text-xl mb-2">
                                <a href="{{ $affiliate->url }}" class="hover:text-primary">
                                    {{ $affiliate->name }}
                                </a>
                            </h2>
                            <div class="prose">
                                {!! \App\Helpers\MarkdownHelper::html($affiliate->content) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why sponsor --}}
    <section class="border-t bg-background-light py-15">
        <div class="container">
            <div class="max-w-170">
                <h2 class="text-3xl font-bold mb-4 text-foreground-title">Pourquoi sponsoriser Grafikart&nbsp;?</h2>
                <p class="mb-4">
                    Si Grafikart vous a aidé à apprendre ou à évoluer dans votre carrière, un don ponctuel ou
                    un sponsoring régulier est une belle façon de dire merci et de permettre de passer le flambeau
                    pour permettre aux autres
                    d'en profiter.
                </p>
                <p class="mb-4">
                    Si vous êtes une entreprise / marque, sponsoriser une vidéo permet de faire découvrir votre produit
                    tout en permettant de financer la création de contenus pédagogiques.
                </p>
                <p>
                    Si vous souhaitez sponsoriser une vidéo ou le site, n'hésitez pas à <a
                        class="underline"
                        href="{{ route('contact') }}">nous contacter</a>
                </p>
            </div>
        </div>
    </section>

@endsection
