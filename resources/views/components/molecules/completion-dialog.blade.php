<x-molecules.dialog class="max-w-110! overflow-visible text-foreground" id="completion">
    <con-fetti>
        <div class="text-center -mt-32">
            <img src="/images/illustrations/success.svg" alt="" class="max-w-75 inline"/>
        </div>
    </con-fetti>
    <h1 class="text-4xl font-bold text-center font-serif mt-4 mb-2">Félicitations !</h1>
    @if($next)
        <p class="text-center text-lg text-pretty mb-4">
            Bien joué ! On poursuit la formation avec une nouvelle vidéo ?
        </p>
        <x-atoms.button href="{{ app_url($next) }}" class="w-full!" size="lg">
            <x-lucide-star/>
            Aller au chapitre suivant
        </x-atoms.button>
    @else
        <p class="text-center text-lg text-pretty mb-4">
            Bien joué ! On poursuit l'apprentissage avec une nouvelle vidéo {{ $course->technology()?->name }} ?
        </p>
        <x-atoms.button href="{{ app_url($course->technology()) }}" class="w-full!" size="lg">
            <x-lucide-star/>
            Découvrir les vidéos {{ $course->technology()?->name }}
        </x-atoms.button>
    @endif
</x-molecules.dialog>
