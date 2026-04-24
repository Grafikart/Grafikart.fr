@extends('front')

@section('title', 'Authentification à deux facteurs')

@section('body')
    <div class="px-4 mt-10 mx-auto max-w-100 pb-20">
            <h1 class="text-foreground-title text-5xl font-bold font-serif text-center mb-8">Authentification à 2 facteurs</h1>

            <x-atoms.card class="p-6">
                <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4" id="form-code">
                    @csrf

                    <x-molecules.field  name="code" label="Code d'authentification" inputmode="numeric" autocomplete="one-time-code" autofocus />

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleRecoveryMode()" class="text-sm text-start text-muted hover:underline">
                            Utiliser un code de récupération
                        </button>
                        <x-atoms.button type="submit" class="w-full">
                            Vérifier
                            <x-lucide-check class="size-4" />
                        </x-atoms.button>
                    </div>
                </form>

                <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4 hidden" id="form-recovery">
                    @csrf

                    <x-molecules.field name="recovery_code" label="Code de récupération" autocomplete="off" />
<div class="flex items-center gap-2">
    <button type="button" onclick="toggleRecoveryMode()" class="text-sm text-start text-muted hover:underline">
        Utiliser un code d'authentification
    </button>
                    <x-atoms.button type="submit" class="w-full">
                        Vérifier
                        <x-lucide-check class="size-4" />
                    </x-atoms.button>
</div>
                </form>
            </x-atoms.card>
    </div>

@endsection

@section('head')
    <script>
        function toggleRecoveryMode() {
            const formCode = document.getElementById('form-code');
            const formRecovery = document.getElementById('form-recovery');
            const isCodeMode = !formCode.classList.contains('hidden');

            formCode.classList.toggle('hidden', isCodeMode);
            formRecovery.classList.toggle('hidden', !isCodeMode);
        }
    </script>
@endsection
