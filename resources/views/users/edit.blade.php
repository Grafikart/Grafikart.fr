@extends('users.user-layout')

@section('title', 'Mon profil')

@section('content')

    <div class="grid gap-10 grid-cols-1 lg:grid-cols-[1fr_300px]">
        <main>
            <section class="flex flex-col gap-4">
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-user class="size-6 text-primary"/>
                    Mes informations
                </h2>

                <form action="" class="contents" method="post">
                    <x-atoms.card class="grid grid-cols-1 md:grid-cols-3 p-4 gap-4">
                        <x-molecules.field name="email" label="Email" :value="$user->email">
                            <x-slot:help class="font-bold">
                            </x-slot>
                        </x-molecules.field>
                        <x-molecules.field name="name" label="Nom d'utilisateur" :value="$user->name"/>
                        <x-molecules.field name="country" label="Pays" type="select" :value="$user->country ?? 'FR'"
                                           :options="\App\Helpers\IntlHelper::countries()">
                        </x-molecules.field>
                        @if(!$user->hasVerifiedEmail())
                        <div class="text-sm text-muted col-span-full -mt-2">
                            Un lien de vérification a été envoyé à cet email.
                            <button type="button" class="underline" onclick="document.getElementById('email-verify').submit()">Renvoyer le lien</button>
                        </div>
                        @endif
                    </x-atoms.card>
                    <x-atoms.button class="ml-auto">
                        Modifier mon profil
                    </x-atoms.button>
                </form>
                <form action="{{ route('verification.send') }}" method="post" class="hidden" id="email-verify"></form>
            </section>

            <section class="flex flex-col gap-4">
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-user-lock class="size-6 text-primary"/>
                    Mot de passe
                </h2>

                <form action="{{ route('users.password') }}" class="contents" method="post">
                    <x-atoms.card class="grid grid-cols-1 md:grid-cols-2 p-4 gap-4">
                        <x-molecules.field name="password" type="password" label="Nouveau mot de passe"/>
                        <x-molecules.field name="password_confirmation" type="password"
                                           label="Confirmer le mot de passe"/>
                    </x-atoms.card>
                    <x-atoms.button class="ml-auto">
                        Modifier mon mot de passe
                    </x-atoms.button>
                </form>
            </section>

            <section class="flex flex-col gap-4">
                <h2 class="text-xl font-bold flex items-center gap-2 text-destructive">
                    <x-lucide-circle-alert class="size-5"/>
                    Zone de non retour
                </h2>

                <p>
                    Vous n'êtes pas satisfait du contenu du site ?<br/>
                    Ou vous souhaitez supprimer toutes les informations associées à ce compte ?
                </p>

                <x-atoms.button variant="destructive" class="ml-auto"
                                onclick="document.getElementById('delete-confirm').showModal()">
                    <x-lucide-trash/>
                    Supprimer mon compte
                </x-atoms.button>
            </section>
        </main>

        <aside class="space-y-8">

            @include('users._subscription')

            <section>
                <h2 class="text-xl font-bold text-foreground-title">Connexion social</h2>
                <p class="mb-4">Reliez votre compte à un réseau social afin de l'utiliser comme mode de connexion</p>
                <div class="space-y-2">
                    @php
                        $drivers = ['github', 'google', 'facebook'];
                    @endphp

                    @foreach($drivers as $driver)
                        @php
                            $field = "{$driver}_id";
                            $isConnected = (bool)$user->getAttribute($field);
                        @endphp
                        <x-atoms.button
                            href="{{ route($isConnected ? 'oauth.unlink' : 'oauth', ['driver' => $driver]) }}"
                            variant="secondary"
                            class="w-full justify-start relative">
                            @switch($driver)
                                @case('github')
                                    <x-lucide-github/>
                                    @break
                                @case('google')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 48 48"
                                    >
                                        <path
                                            fill="currentColor"
                                            d="M24.7 20.5v7.6h10.9a10.9 10.9 0 0 1-10.9 8 12.1 12.1 0 1 1 7.9-21.3l5.6-5.6A20 20 0 1 0 24.7 44c16.8 0 20.5-15.7 18.9-23.5Z"

                                        />
                                    </svg>
                                    @break;
                                @case('facebook')
                                    <x-lucide-facebook/>
                                    @break;
                            @endswitch
                            {{ $isConnected ? 'Dissocier' : 'Lier' }} votre compte {{ ucfirst($driver) }}
                        </x-atoms.button>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
    <x-molecules.dialog id="delete-confirm" class="max-w-87" title="Confirmer la suppression">
        <form action="{{ route('users.delete') }}" class="space-y-4" method="post">
            @method('DELETE')
            <p>
                Vous êtes sur le point de supprimer votre compte Grafikart. Cette action est définitive !
            </p>
            <x-molecules.field name="password" type="password" label="Entrez votre mot de passe pour confirmer"/>
            <x-molecules.field
                name="reason"
                type="textarea"
                placeholder="Si vous n'avez pas aimé quelque chose c'est le moment de vous exprimer"
                label="Pourquoi vous nous quittez :("
            />
            <x-atoms.button variant="destructive" class="ml-auto"
                            onclick="document.getElementById('delete-confirm').showModal()">
                <x-lucide-trash/>
                Confirmer la suppression
            </x-atoms.button>
        </form>

    </x-molecules.dialog>

@endsection
