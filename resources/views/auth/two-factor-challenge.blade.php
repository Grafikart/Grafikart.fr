@extends('front')

@section('title', 'Authentification à deux facteurs')

@section('body')
    <div class="px-4 mt-10 mx-auto max-w-100 pb-20">
            <h1 class="text-page-title text-center mb-8">Vérification 2FA</h1>

            <x-atoms.card class="p-6">
                <p class="mb-4 text-sm text-muted" id="description-code">
                    Veuillez entrer le code de votre application d'authentification.
                </p>
                <p class="mb-4 text-sm text-muted hidden" id="description-recovery">
                    Veuillez entrer l'un de vos codes de récupération d'urgence.
                </p>

                <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4" id="form-code">
                    @csrf

                    <x-molecules.field name="code" label="Code d'authentification" inputmode="numeric" autocomplete="one-time-code" autofocus />

                    <x-atoms.button type="submit" class="w-full">
                        Vérifier
                        <x-lucide-check class="size-4" />
                    </x-atoms.button>
                </form>

                <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4 hidden" id="form-recovery">
                    @csrf

                    <x-molecules.field name="recovery_code" label="Code de récupération" autocomplete="off" />

                    <x-atoms.button type="submit" class="w-full">
                        Vérifier
                        <x-lucide-check class="size-4" />
                    </x-atoms.button>
                </form>

                <div class="mt-4 text-center">
                    <button
                        type="button"
                        class="text-sm text-primary hover:underline"
                        onclick="toggleRecoveryMode()"
                        id="toggle-button"
                    >
                        Utiliser un code de récupération
                    </button>
                </div>
            </x-atoms.card>
    </div>

    <script>
        function toggleRecoveryMode() {
            const formCode = document.getElementById('form-code');
            const formRecovery = document.getElementById('form-recovery');
            const descCode = document.getElementById('description-code');
            const descRecovery = document.getElementById('description-recovery');
            const toggleBtn = document.getElementById('toggle-button');

            const isCodeMode = !formCode.classList.contains('hidden');

            formCode.classList.toggle('hidden', isCodeMode);
            formRecovery.classList.toggle('hidden', !isCodeMode);
            descCode.classList.toggle('hidden', isCodeMode);
            descRecovery.classList.toggle('hidden', !isCodeMode);
            toggleBtn.textContent = isCodeMode
                ? "Utiliser le code d'authentification"
                : 'Utiliser un code de récupération';
        }
    </script>
@endsection
