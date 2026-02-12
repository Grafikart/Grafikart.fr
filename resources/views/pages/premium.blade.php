@extends('front')

@section('title', 'Devenir premium')

@section('head')
    <meta name="stripe" content="{{ config('services.stripe.public') }}">
@endsection

@section('body')

    <div class="bg-background-light container pb-10">
        <h1 class="text-page-title mb-4">Devenir premium</h1>
        <p class="text-lg max-w-250">
            Devenir premium sur Grafikart, c'est <strong>soutenir</strong> la création de nouveaux contenus chaque
            semaine
            et accéder à du contenu exclusif pour apprendre et s'améliorer (comme le téléchargement des vidéos et des
            sources).
        </p>

    </div>


    <div
        class="border-t bg-background container grid grid-cols-1 md:grid-cols-[500px_1fr] gap-8 md:gap-16 py-10 md:pt-15 md:pb-20" style="--width:1100px;">
        <x-organisms.premium :plans="$plans"/>
        <div>
            <div class="prose prose-lg">
                <h2>
                    Pourquoi cette offre ?
                </h2>
                <p>
                    Mon but à travers grafikart.fr est de partager mes connaissances avec le plus grand nombre, c'est
                    pourquoi
                    j'essaie de rendre un maximum de contenu gratuit et public.
                </p>
                <p>
                    Malgré tout, la préparation, l'enregistrement et le montage des tutoriels prend un temps
                    considérable
                    (10 à
                    20 heures par semaine). Du coup proposer des options payantes, comme le téléchargement des sources,
                    me
                    permet d'amortir une partie du temps passé et de continuer à faire vivre le site.
                </p>

                <h2>Vous êtes une école<br> ou un organisme de formation ?</h2>
                <p>
                    Si vous souhaitez utiliser les contenus de Grafikart comme support dans le cadre de la formation de
                    vos
                    étudiants
                    vous pouvez demander l'accès à un compte "École" qui vous permettra de créer des comptes premiums
                    automatiquement et de suivre la progression de vos étudiants sur la plateforme.
                </p>
            </div>
            <x-atoms.button variant="outline" href="/premium/ecoles" class="mt-4">
                En savoir plus
            </x-atoms.button>
        </div>
    </div>

    @if(request()->boolean('success'))
        <x-molecules.dialog class="max-w-110 overflow-visible text-foreground">
            <con-fetti>
                <div class="text-center -mt-28.75">
                    <img src="/images/illustrations/payment.svg" alt="" class="max-w-50 inline" />
                </div>
            </con-fetti>
            <h1 class="text-3xl font-bold text-center font-sserif">Merci</h1>
            <p class="text-center text-lg text-pretty">
                Vous êtes maintenant premium jusqu'au<br/>
                <strong>{{ auth()->user()?->premium_end_at?->translatedFormat('d F Y') }}</strong>.
            </p>
            <x-atoms.button href="{{ route('courses.index', ['premium' => 1]) }}" class="w-full" size="lg">
                <x-lucide-star />
                Voir les tutoriels premiums
            </x-atoms.button>
        </x-molecules.dialog>
        <script>document.querySelector('dialog').showModal()</script>
    @endif

@endsection
