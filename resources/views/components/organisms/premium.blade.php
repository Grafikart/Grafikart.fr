@props(['plans'])

<div>
    @if ($plans->count() > 1)
        <x-atoms.tabs as="nav-tabs" class="mb-8 mx-auto">
            @foreach ($plans as $plan)
                <x-atoms.tab
                    href="#plan{{ $plan->id }}"
                    :active="$loop->first"
                >
                    {{ $plan->name }}
                </x-atoms.tab>
            @endforeach
        </x-atoms.tabs>
    @endif

    <div>
        @foreach ($plans as $plan)
            <x-atoms.card
                class="max-w-100 mx-auto overflow-auto text-center"
                id="plan{{ $plan->id }}"
                :hidden="!$loop->first ?: null"
            >
                <div class="px-4 pt-8 text-[100px] font-bold leading-none text-primary">
                    {{ $plan->price }}<sup class="whitespace-nowrap text-xl">€ TTC</sup>
                </div>

                <div class="border-b py-5 text-lg flex flex-col gap-1">
                    Visionner les tutoriels
                    <strong>en avance</strong>
                </div>
                <div class="border-b py-5 text-lg flex flex-col gap-1">
                    Voir les vidéos
                    <a href="/premium" class="hover:underline flex gap-2 items-center mx-auto w-max font-bold text-yellow"><x-lucide-star class="size-4"/> premium</a>
                </div>
                <div class="border-b py-5 text-lg flex flex-col gap-1">
                    <strong>Télécharger</strong>
                    les vidéos
                </div>
                <div class="border-b py-5 text-lg flex flex-col gap-1">
                    <strong>Télécharger</strong>
                    les sources
                </div>
                <div class="p-4 bg-background">
                    <x-atoms.button
                        size="lg"
                        class="w-full mx-auto cursor-pointer items-baseline"
                        as="premium-button"
                        duration="{{ $plan->duration }}"
                        plan="{{ $plan->id }}"
                        price="{{ $plan->price }}"
                        paypalid="{{ config('services.paypal.id') }}"
                    >
                        Devenir premium
                        <div class="opacity-70 font-medium text-sm">({{ $plan->name }})</div>
                    </x-atoms.button>
                </div>
            </x-atoms.card>
        @endforeach
    </div>
</div>
