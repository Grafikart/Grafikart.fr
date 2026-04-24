@extends('users.user-layout')

@section('title', 'Authentification à 2 facteurs')

@section('content')

    <x-molecules.dialog open class="w-175!">
        <section class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                <x-lucide-shield class="size-6 text-primary"/>
                Mon profil
            </h2>

            <p class="text-sm text-muted">
                Scannez le QR code avec votre application d'authentification (Google Authenticator, Authy, etc.), puis
                entrez le code généré pour confirmer la configuration.
            </p>
            <div class="p-4 flex flex-col items-center gap-4">
                <div class="size-48 [&_svg]:w-full [&_svg]:h-full">{!! $twoFactorQrCode !!}</div>
            </div>
            <form action="{{ route('two-factor.confirm')}}" method="post" class="contents">
                @csrf
                <x-molecules.field name="code" label="Code de confirmation" bag="confirmTwoFactorAuthentication"
                                   inputmode="numeric" autocomplete="one-time-code" autofocus/>

                <div class="flex items-center justify-end gap-2">
                    <x-atoms.button href="{{ route('users.edit') }}" variant="secondary">
                        Annuler
                    </x-atoms.button>
                    <x-atoms.button type="submit">
                        <x-lucide-check class="size-4"/>
                        Confirmer la configuration
                    </x-atoms.button>
                </div>
            </form>
        </section>


    </x-molecules.dialog>

@endsection
