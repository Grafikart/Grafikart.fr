@props(['chapters', 'active' => null])

@php
    $activeChapterTitle = $active ? collect($chapters)->firstWhere(fn ($chapter) => collect($chapter['courses'])->firstWhere('id', $active))['title'] : $chapters[0]['title'];
@endphp
<x-atoms.card class="pt-4 border block" as="course-chapters">
    <div class="text-sm uppercase text-muted px-4 pb-4 border-b">Sommaire de la formation</div>
    <div class="max-h-200 overflow-auto">
        @foreach ($chapters as $k => $chapter)
            <details class="group" @if($chapter['title'] === $activeChapterTitle) open @endif>
                <summary
                    class="appearance-none p-4 py-2 bg-background/50 hover:bg-background group:first:border-t-0 border-t flex items-center gap-2 cursor-pointer">
                    <div class="mr-auto">
                        <div class="text-muted text-sm">Chapitre {{$k + 1}}</div>
                        <h3 class="text-title font-bold">{{ $chapter['title'] }}</h3>
                    </div>
                    <div class="text-muted text-xs group-open:hidden">
                        {{ count($chapter['courses']) }} vidéos
                    </div>
                    <x-lucide-chevron-down class="size-4 text-muted group-open:rotate-180"/>
                </summary>
                @foreach ($chapter['courses'] as $index => $course)
                    <x-molecules.chapter
                        :active="$course->id === $active"
                        :chapter="$course"
                        :index="$index + 1"
                    />
                @endforeach
            </details>
        @endforeach
    </div>
</x-atoms.card>
