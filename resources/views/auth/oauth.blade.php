<h2 class="mt-8 mb-4 text-foreground-title font-serif font-semibold text-3xl">Utiliser les réseaux
    sociaux</h2>

<div class="space-y-4">
    <x-atoms.button href="{{ route('oauth', ['driver' => 'github']) }}" size="lg" class="bg-[#444]!  w-full! relative!">
        <x-lucide-github class="absolute left-5 top-1/2 -translate-y-1/2"/>
        Se connecter avec GitHub
    </x-atoms.button>

    <x-atoms.button href="{{route('oauth', ['driver' => 'google'])}}" size="lg" class="bg-[#dd4b39]!  w-full! relative!">
        <svg xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 48 48"
             class="absolute left-5 top-1/2 -translate-y-1/2 size-4">
            <path
                fill="currentColor"
                d="M24.7 20.5v7.6h10.9a10.9 10.9 0 0 1-10.9 8 12.1 12.1 0 1 1 7.9-21.3l5.6-5.6A20 20 0 1 0 24.7 44c16.8 0 20.5-15.7 18.9-23.5Z"

            />
        </svg>
        Se connecter avec Google
    </x-atoms.button>

    <x-atoms.button href="{{route('oauth', ['driver' => 'facebook'])}}" size="lg" class="bg-[#47639e]!  w-full! relative!">
        <x-lucide-facebook class="absolute left-5 top-1/2 -translate-y-1/2"/>
        Se connecter avec Facebook
    </x-atoms.button>
</div>
