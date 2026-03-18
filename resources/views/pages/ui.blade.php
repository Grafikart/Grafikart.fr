@extends('front')

@section('body')

    <div class="container">

        <h2 class="text-4xl font-bold font-serif mb-4">Boutons</h2>

        <div class="flex flex-col gap-2 *:w-max">
            <div class="flex items-center gap-2">
                @foreach(['primary', 'secondary', 'outline', 'ghost', 'destructive'] as $variant)
                    <x-atoms.button :variant="$variant"><x-lucide-youtube /> Bouton {{ $variant }}</x-atoms.button>
                @endforeach
            </div>
            <x-atoms.button size="sm">Bouton sm</x-atoms.button>
            <x-atoms.button size="lg">Bouton lg</x-atoms.button>
            <x-atoms.button size="icon"><x-lucide-youtube /></x-atoms.button>
        </div>

        <h2 class="mt-12 mb-4 text-4xl font-bold font-serif">Progression</h2>

        <div class="max-w-4xl">
            <x-atoms.progress-bar :current="1" :total="58" />
        </div>

    </div>

@endsection
