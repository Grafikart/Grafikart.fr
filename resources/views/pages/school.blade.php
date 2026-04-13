@extends('front')

@section('title', 'Écoles')

@section('body')

    <section class="grid md:grid-cols-[1fr_470px] container items-center gap-6 pb-15 bg-background-light">
        <div class="space-y-4 max-w-200">
            <h1 class="text-balance text-3xl md:text-5xl font-serif text-foreground-title font-bold ">
                Vous êtes une
                <span class="text-primary">école</span> ou un professionnel de l'enseignement ?
            </h1>
            <p class="text-xl">
                Si vous souhaitez utiliser les contenus de Grafikart comme support dans le cadre de la formation de vos
                étudiants vous pouvez demander l'accès à un compte "École".
            </p>
            <x-atoms.button :href="route('contact')" size="lg" variant="outline">
                <x-lucide-mail/>
                Obtenir un devis
            </x-atoms.button>
        </div>
        <div class="justify-self-end hidden md:block">
            <img src="/images/illustrations/podcast.svg" width="473" height="367" alt=""
                 class="max-w-110">
        </div>
    </section>

    <section class="flex flex-col md:flex-row container py-20 md:items-center gap-15">
        <div class="max-w-100">
            <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance">
                Dashboard
                <div class="text-primary">personnalisé</div>
            </h2>
            <p class="text-2xl text-pretty text-muted mt-4">Une interface d'administration vous permet de suivre la
                progression de vos étudiants sur les formations proposées sur le site.

            </p>
        </div>

        <x-atoms.card class="p-4 flex-1">
            <table class="table w-full">
                <thead>
                <tr>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Fin d'abonnement</th>
                    <th class="text-end">Nombres de cours complétés</th>
                </tr>
                </thead>
                <tbody>
                @php $today = now(); @endphp
                @foreach(range(1, 6) as $i)
                    <tr class="last:border-none last:*:pb-2">
                        <td> etudiant{{ $i }}@mon-ecole.fr</td>
                        <td class="text-small text-muted">
                            {{ $today->copy()->subDays(rand(1, 3) * 30)->translatedFormat('d F Y') }}
                        </td>
                        <td class="text-small text-muted">
                            {{ $today->copy()->addDays(rand(1, 3) * 30)->translatedFormat('d F Y') }}
                        </td>
                        <td class="text-right">{{ rand(20, 100) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </x-atoms.card>

    </section>

    <section class="flex flex-col md:flex-row container py-20 md:items-center gap-15">

        <div class="max-w-100 md:order-1">
            <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance">
                Suivi au
                <div class="text-primary">cas par cas</div>
            </h2>
            <p class="text-2xl text-pretty text-muted mt-4">Vous pouvez voir le détail de la progression de chacun des
                étudiants pour un suivi plus fin et adapter l'enseignement en fonction de sa progression. </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 flex-1">
            @foreach($formations as $formation)
                <x-molecules.student-progress-card :item="$formation"/>
            @endforeach
        </div>
    </section>


    <section class="flex flex-col md:flex-row container py-20 md:items-center gap-15">

        <div class="max-w-150">
            <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance">
                Une formule
                <div class="text-primary">sur mesure</div>
            </h2>
            <p class="text-2xl text-pretty text-muted mt-4">
                Spécifiez le nombre de mois premium que vous désirez utiliser pour vos étudiants et vous pourrez ensuite
                les distribuer comme vous le souhaitez. Un import CSV vous permettra de donner accès au site à une liste
                d'étudiant (pour la rentrée par exemple).
            </p>
        </div>

        <div class="flex-1">
            <x-atoms.card padded class="max-w-100 mx-auto p-6">
                <h2 class="text-lg font-bold mb-1">Importer des étudiants</h2>
                <p class="text-md mb-4">
                    Il vous reste <strong>42 mois</strong> premium à donner à vos étudiants.
                </p>
                <div class="space-y-2">
                    <x-molecules.field type="file" label="Fichier CSV" name="csv"/>
                    <x-molecules.field type="text" label="Sujet de l'email" name="email"
                                       value="Compte premium Grafikart.fr"/>
                    <x-molecules.field input-class="h-20" type="textarea" label="Message" name="message"
                                       placeholder="Message envoyé avec le code aux étudiants"/>
                </div>
            </x-atoms.card>
        </div>
    </section>



    <section class="container py-20" style="--width: 700px;">
        <h2 class="text-6xl font-serif font-bold text-foreground-title text-balance">
            Des besoins
            <div class="text-primary">spécifiques ?</div>
        </h2>
        <p class="text-2xl text-pretty text-muted my-4">
            Vous avez des besoins spécifiques qui ne sont pas déjà couverts par les fonctionnalités précédentes ?
            N'hésitez pas à me contacter, car certains développements peuvent être faits pour adapter la plateforme
            à vos besoins.
        </p>

        <x-atoms.button :href="route('contact')" size="lg" variant="outline">
            <x-lucide-mail/>
            Obtenir un devis
        </x-atoms.button>

    </section>

@endsection
