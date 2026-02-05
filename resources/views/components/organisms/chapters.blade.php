@props(['chapters', 'active' => null])

    <x-atoms.card class="pt-4 border max-h-200 overflow-auto block" as="course-chapters">
        <div class="text-sm uppercase text-muted px-4 pb-4">Sommaire de la formation</div>
        @foreach ($chapters as $k => $chapter)
                <div class="p-4 py-2 bg-background border-t">
                    <div class="text-muted text-sm">Chapitre {{$k + 1}}</div>
                    <h3 class="text-title font-bold">{{ $chapter['title'] }}</h3>
                </div>
                @foreach ($chapter['courses'] as $index => $course)
                    <x-molecules.chapter
                        :active="$course->id === $active"
                        :chapter="$course"
                        :index="$index + 1"
                    />
                @endforeach
        @endforeach
    </x-atoms.card>
