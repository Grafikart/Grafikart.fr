@extends('users.user-layout')

@section('title', 'Mon profil')

@section('content')

    <x-molecules.dialog open class="w-175!">
        <section class="flex flex-col gap-4">
            <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                <x-lucide-shield class="size-6 text-primary"/>
                Codes de récupération
            </h2>

            <p class="text-sm text-muted">
                L'authentification à 2 facteurs a bien été activée. <br/>
                Des codes de récupération ont été générés et permettent d'accéder à votre compte si vous perdez votre application d'authentification. Conservez ces codes en lieu sûr.
            </p>
            <div class="grid grid-cols-2 gap-1 font-mono text-sm bg-muted/30 p-3 rounded-lg">
                @foreach($codes as $code)
                    <span>{{ $code }}</span>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-2">
                <x-atoms.button type="submit" href="{{ route('users.edit') }}">
                    <x-lucide-notebook-pen class="size-4"/>
                    C'est noté !
                </x-atoms.button>
            </div>
        </section>

    </x-molecules.dialog>

@endsection
