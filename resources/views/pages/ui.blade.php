@extends('front')

@section('title', 'UI')

@section('head')
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('body')

    <div class="container py-8 space-y-16">

        {{-- Boutons --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Boutons</h2>
            <div class="flex flex-col gap-2 *:w-max">
                @foreach(['md', 'sm', 'lg', 'icon'] as $size)
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach(['primary', 'secondary', 'outline', 'ghost', 'destructive'] as $variant)
                        <x-atoms.button :size="$size" :variant="$variant">
                            <x-lucide-youtube />
                            @if($size !== 'icon')
                                Bouton {{ $variant }}
                            @endif
                        </x-atoms.button>
                    @endforeach
                </div>
                @endforeach
            </div>
        </section>

        {{-- Badges --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Badges</h2>
            <div class="flex items-center gap-2 flex-wrap">
                @foreach(['info', 'success', 'destructive'] as $variant)
                    <x-atoms.badge :variant="$variant">Badge {{ $variant }}</x-atoms.badge>
                @endforeach
            </div>
        </section>

        {{-- Alertes --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Alertes</h2>
            <div class="max-w-2xl space-y-3">
                <x-atoms.alert type="info">Ceci est une alerte de type info avec un message important.</x-atoms.alert>
            </div>
        </section>

        {{-- Cards --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Cards</h2>
            <div class="flex gap-4 flex-wrap">
                <x-atoms.card class="p-4 w-48">Card simple</x-atoms.card>
                <x-atoms.card :padded="true" class="w-48">Card padded</x-atoms.card>
            </div>
        </section>

        {{-- Séparateur --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Séparateur</h2>
            <div class="max-w-2xl space-y-4">
                <x-atoms.separator />
                <div class="flex items-center gap-4 h-8">
                    <span>Gauche</span>
                    <x-atoms.separator orientation="vertical" class="h-full" />
                    <span>Droite</span>
                </div>
            </div>
        </section>

        {{-- Formulaires --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Formulaires</h2>
            <div class="max-w-lg space-y-4">
                <div class="space-y-1">
                    <x-atoms.label for="demo-input">Label</x-atoms.label>
                    <x-atoms.input id="demo-input" placeholder="Placeholder..." />
                </div>
                <div class="space-y-1">
                    <x-atoms.label for="demo-textarea">Textarea</x-atoms.label>
                    <x-atoms.input type="textarea" id="demo-textarea" placeholder="Contenu..." />
                </div>
                <div class="space-y-1">
                    <x-atoms.label for="demo-select">Select</x-atoms.label>
                    <x-atoms.input type="select" id="demo-select">
                        <option value="">Choisir...</option>
                        <option value="a">Option A</option>
                        <option value="b">Option B</option>
                    </x-atoms.input>
                </div>
                <div class="flex items-center gap-2">
                    <x-atoms.checkbox id="demo-checkbox" />
                    <x-atoms.label for="demo-checkbox">Checkbox</x-atoms.label>
                </div>
                <div class="flex items-center gap-2">
                    <x-atoms.checkbox id="demo-checkbox-checked" :checked="true" />
                    <x-atoms.label for="demo-checkbox-checked">Checkbox cochée</x-atoms.label>
                </div>
            </div>
        </section>

        {{-- Switch --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Switch</h2>
            <div class="flex items-center gap-6 flex-wrap">
                <div class="flex items-center gap-2">
                    <x-atoms.switch id="switch-off" />
                    <x-atoms.label for="switch-off">Désactivé</x-atoms.label>
                </div>
                <div class="flex items-center gap-2">
                    <x-atoms.switch id="switch-on" :checked="true" />
                    <x-atoms.label for="switch-on">Activé</x-atoms.label>
                </div>
                <div class="flex items-center gap-2">
                    <x-atoms.switch id="switch-sm" size="sm" />
                    <x-atoms.label for="switch-sm">Small</x-atoms.label>
                </div>
                <div class="flex items-center gap-2">
                    <x-atoms.switch id="switch-sm-on" size="sm" :checked="true" />
                    <x-atoms.label for="switch-sm-on">Small activé</x-atoms.label>
                </div>
            </div>
        </section>

        {{-- Field --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Field</h2>
            <div class="max-w-lg space-y-4">
                <x-molecules.field name="email" label="Email" type="email" placeholder="exemple@email.com" />
                <x-molecules.field name="message" label="Message" type="textarea" help="Minimum 20 caractères." />
            </div>
        </section>

        {{-- Onglets --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Onglets</h2>
            <div class="space-y-6">
                <div>
                    <p class="text-sm text-muted mb-2">Default</p>
                    <x-atoms.tabs>
                        <x-atoms.tab href="#tab1" :active="true">Onglet 1</x-atoms.tab>
                        <x-atoms.tab href="#tab2">Onglet 2</x-atoms.tab>
                        <x-atoms.tab href="#tab3">Onglet 3</x-atoms.tab>
                    </x-atoms.tabs>
                </div>
                <div>
                    <p class="text-sm text-muted mb-2">Pill</p>
                    <x-atoms.tabs variant="pill">
                        <x-atoms.tab href="#tab1" variant="pill" :active="true">Onglet 1</x-atoms.tab>
                        <x-atoms.tab href="#tab2" variant="pill">Onglet 2</x-atoms.tab>
                        <x-atoms.tab href="#tab3" variant="pill">Onglet 3</x-atoms.tab>
                    </x-atoms.tabs>
                </div>
            </div>
        </section>

        {{-- Progression --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Progression</h2>
            <div class="max-w-4xl">
                <x-atoms.progress-bar :current="1" :total="58" />
            </div>
        </section>

        {{-- Duration / Ago --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Durée & Date</h2>
            <div class="flex items-center gap-6 flex-wrap">
                <div>
                    <p class="text-sm text-muted mb-1">Duration</p>
                    <x-atoms.duration :duration="3720" />
                </div>
                <div>
                    <p class="text-sm text-muted mb-1">Duration (minutes)</p>
                    <x-atoms.duration :duration="540" />
                </div>
                <div>
                    <p class="text-sm text-muted mb-1">Ago</p>
                    <x-atoms.ago :date="now()->subHours(3)" />
                </div>
            </div>
        </section>

        {{-- Toast --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Toast</h2>
            <div class="relative h-32 border rounded-lg overflow-hidden">
                <div class="absolute inset-0 flex items-start justify-center pt-4 gap-4 flex-wrap">
                    <x-atoms.toast type="success" message="Opération réussie !" class="relative mx-0" />
                    <x-atoms.toast type="error" message="Une erreur est survenue." class="relative mx-0" />
                </div>
            </div>
        </section>

        {{-- Dialog --}}
        <section>
            <h2 class="text-4xl font-bold font-serif mb-4">Dialog</h2>
            <x-atoms.button commandfor="demo-dialog" command="show-modal">Ouvrir la dialog</x-atoms.button>
            <x-molecules.dialog id="demo-dialog" title="Titre de la dialog" class="max-w-100">
                <p class="mt-4 text-muted">Contenu de la dialog. Cliquez sur la croix ou en dehors pour fermer.</p>
                <div class="mt-6 flex justify-end gap-2">
                    <x-atoms.button variant="outline" commandfor="demo-dialog" command="close">Annuler</x-atoms.button>
                    <x-atoms.button>Confirmer</x-atoms.button>
                </div>
            </x-molecules.dialog>
        </section>

    </div>

@endsection
