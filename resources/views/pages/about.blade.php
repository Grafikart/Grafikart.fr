@extends('front', ['class' => 'bg-background-light'])

@section('title', 'Mon environnement')

@section('body')

    <h1 class="text-page-title mb-4 container pb-10">Mon environnement</h1>

    <div
        class="border-t bg-background container grid grid-cols-1 md:grid-cols-[400px_1fr] gap-8 md:gap-16 py-10 md:pt-15 md:pb-20">

        <div class="space-y-4 md:mt-30">
            <h2 class="text-5xl font-serif font-bold text-foreground-title">Mon <span class="text-primary">éditeur</span></h2>
            <p class="text-lg">
                Au fil des vidéos je suis amené à utiliser plusieurs éditeurs. Aussi vous êtes assez nombreux à
                poser cette fameuse question "Quel éditeur tu utilises ?"
            </p>
        </div>
        <div>
            <x-atoms.tabs class="mb-4 mx-auto block" as="nav-tabs">
                <x-atoms.tab href="#phpstorm" aria-controls="phpstorm" aria-selected="true">
                    <img src="/images/icons/phpstorm.svg" class="size-5" alt=""/>
                    PHPStorm
                </x-atoms.tab>
                <x-atoms.tab href="#vscode" aria-controls="vscode" role="tab">
                    <img src="/images/icons/vscode.svg" alt="" class="size-5">
                    VScode
                </x-atoms.tab>
            </x-atoms.tabs>
            <div>
                <div id="phpstorm" class="space-y-5">
                    <a class="block rounded-md overflow-hidden shadow-md"
                       href="{{ asset('images/env/phpstorm.jpg') }}" target="_blank">
                        <img src="{{ asset('images/env/phpstorm.jpg') }}" alt="Interface PHPStorm" class="card-big">
                    </a>
                    <div class="prose prose-lg mt-6 max-w-175 mx-auto">
                        <p>
                            Dans la plupart des vidéos j'utilise l'éditeur <a
                                href="https://www.jetbrains.com/phpstorm/">PHPStorm</a>
                            car il offre tout un ensemble d'outil qui me permettent de travailler de manière plus
                            rapide et plus
                            efficace.
                        </p>
                        <p>
                            J'utilise actuellement le thème <a
                                href="https://plugins.jetbrains.com/plugin/15662-tokyo-night-color-scheme?preview=true">Tokyo
                                Night</a> avec l'extension <a
                                href="https://plugins.jetbrains.com/plugin/8006-material-theme-ui">Material
                                Theme</a>. Pour la police
                            j'utilise
                            <a href="https://www.jetbrains.com/lp/mono/">Jetbrains Mono</a> qui est installée par
                            défaut avec
                            l'éditeur.
                        </p>
                    </div>
                </div>
                <div id="vscode" class="space-y-5" hidden>
                    <a href="{{ asset('images/env/vscode.jpg') }}" target="_blank">
                        <img src="{{ asset('images/env/vscode.jpg') }}" alt="Interface Visual Studio Code"
                             class="card-big">
                    </a>
                    <div class="prose prose-lg mt-6 max-w-175 mx-auto">
                        <p>
                            <a href="https://code.visualstudio.com/">Visual studio code</a> est un éditeur que
                            j'utilise en général
                            dans
                            les vidéos qui ciblent un public plus débutant. L'éditeur est plus accessible et je
                            préfère avoir un
                            environnement
                            au plus proche de la personne qui va suivre la vidéo.
                        </p>
                        <p>
                            En revanche, je ne l'utilise pas quotidiennement car il offre une auto-complétion moins
                            performante sur
                            le language PHP
                            et n'offre pas forcément tous les outils que je suis habitué à utiliser sur PHPStorm.
                        </p>
                        <p>
                            Dans la plupart des vidéos j'utilise un éditeur sans configuration pour avoir une
                            interface familière
                            mais voici quelques extensions incontournable que j'utilise pour ma propose installation
                            de l'éditeur
                        </p>
                        <ul>
                            <li>Le thème <a
                                    href="https://marketplace.visualstudio.com/items?itemName=enkia.tokyo-night">Tokyo
                                    night</a></li>
                            <li><a href="https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer">LiveServer</a>
                                permet de lancer un serveur web qui actualisera la page à chaque changement de
                                fichier.
                            </li>
                            <li><a href="https://marketplace.visualstudio.com/items?itemName=2gua.rainbow-brackets">Rainbow
                                    Brackets</a> colorise les parenthèse / crochets et permet de mieux s'y repérer
                                dans la ponctuation.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="bg-background-light container py-20">
        <div class="max-w-175 mx-auto">
            <h2 class="text-5xl font-serif font-bold mb-4 text-foreground-title">
                Mon système<br/>
                <span class="text-primary">
                    d'exploitation
                </span>
            </h2>
            <p class="text-xl">
                Mon système d'exploitation principal (celui sur lequel je passe le plus de temps) est <a
                    href="https://archlinux.fr/">Arch Linux</a>.
            </p>
        </div>
        <x-atoms.lazy-video video="C5BSFB4_il4" poster="/images/env/phpstorm.jpg" class="my-8 max-w-250 mx-auto"/>
        <div class="prose prose-lg max-w-175 mx-auto">
            <p>
                Cette distribution dispose d'un système de "rolling release" qui permet de disposer des dernière
                versions
                des logiciels en continu (pour le meilleur et pour le pire). Un dépôt supplémentaire et accessible par
                la
                communauté,
                le Arch User Repository, permet aussi d'installer simplement et rapidement des application plus
                spécifiques.
            </p>
            <p>
                Mon environnement de bureau (l'interface qui permet d'intéragir avec le système) est <a
                    href="https://i3wm.org/">Hyprland</a>. C'est un environnement un peu particulier qui dispose les fenêtres
                sous formes de mosaïques plutôt que d'avoir des fenêtres flottantes.
            </p>
        </div>
    </div>

    <div class="bg-background container py-20">
        <h2 class="text-6xl font-serif font-bold mb-4 text-center text-foreground-title">
            Quels <span class="text-primary">logiciels</span><br/> j'utilise
        </h2>
        <x-atoms.lazy-video video="GhTTjvi8HZk" poster="/images/env/desktop.jpg" class="my-8 max-w-250 mx-auto"/>

    </div>

@endsection
