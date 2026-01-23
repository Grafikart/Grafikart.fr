@props(['chapters'])

<section class="space-y-3">
    <h2 class="font-serif font-bold text-2xl flex items-center gap-2">
        <x-lucide-book-open-text class="size-5"/>
        Chapitres
    </h2>

    <x-atoms.card class="space-y-4 pt-4">
        @foreach ($chapters as $chapter)
            <div class="space-y-2">
                <h3 class="px-4 text-xl font-serif font-bold">{{ $chapter['title'] }}</h3>
                <div>
                    @foreach ($chapter['courses'] as $index => $course)
                        <x-molecules.chapter :active="$index === 1" :chapter="$course" :index="$index + 1"/>
                    @endforeach
                </div>
            </div>
        @endforeach
    </x-atoms.card>
</section>
