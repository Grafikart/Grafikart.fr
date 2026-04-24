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
                        @if($user->isPremium())
                        <div class="col-span-full flex items-center gap-2">
                            <input type="hidden" name="html5_player" value="0">
                            <x-atoms.switch name="html5_player" value="1" :checked="$user->html5_player" id="html5_player"/>
                            <label for="html5_player" class="text-sm">Utiliser le lecteur HTML5 pour toutes les vidéos</label>
                        </div>
                        @endif
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
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-shield class="size-6 text-primary"/>
                    Authentification à deux facteurs
                </h2>

                @if($twoFactorEnabled)
                    <p>
                        L'authentification à deux facteurs est <strong class="text-success">activée</strong>.
                        À chaque connexion, vous devrez entrer un code depuis votre application d'authentification.
                    </p>

                    <div class="flex items-center justify-end gap-2">
                        <form action="{{ route('two-factor.regenerate-recovery-codes') }}" method="post">
                            @csrf
                            <x-atoms.button type="submit" variant="secondary" size="sm">
                                <x-lucide-refresh-ccw-dot class="size-4"/>
                                Régénérer les codes de récupérations
                            </x-atoms.button>
                        </form>
                        <form action="{{ route('two-factor.disable' )}}" method="post">
                            @csrf
                            @method('DELETE')
                            <x-atoms.button type="submit" variant="destructive" size="sm">
                                <x-lucide-shield-off class="size-4"/>
                                Désactiver la 2FA
                            </x-atoms.button>
                        </form>
                    </div>
                @else
                    <p>
                        Protégez votre compte en activant la double authentification. Vous aurez besoin d'une application comme
                        <a href="https://getaegis.app/" class="underline">Aegis</a>.
                    </p>
                    <form action="{{ url('/user/two-factor-authentication') }}" method="post">
                        @csrf
                        <x-atoms.button type="submit" class="ml-auto">
                            <x-lucide-shield class="size-4"/>
                            Activer la 2FA
                        </x-atoms.button>
                    </form>
                @endif
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
                <h2 class="text-xl font-bold text-foreground-title mb-1">Coupon</h2>
                <p class="mb-4 text-muted">Si vous avez un code promotionnel / code d'école vous pouvez l'insérer ici</p>
                <form method="post" action="{{ route('users.coupon') }}" class="flex">
                   <x-atoms.input placeholder="Code" name="coupon" label="Coupon" :value="old('coupon')" />
                   <x-atoms.button type="submit" variant="primary" class="w-max flex-none">Utiliser ce code</x-atoms.button>
                </form>
                @error('coupon')
                <p class="text-sm text-destructive mt-2">{{ $message }}</p>
                @enderror
            </section>

            <section>
                <h2 class="text-xl font-bold text-foreground-title mb-1">Connexion social</h2>
                <p class="mb-4 text-muted">Reliez votre compte à un réseau social afin de l'utiliser comme mode de connexion</p>
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
                            {{ $isConnected ? 'Dissocier de ' : 'Lier avec ' }} {{ ucfirst($driver) }}
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
