@extends('users.user-layout')

@section('title', 'Mon historique')

@section('content')
    <main class="max-w-container mx-auto pb-20">
        <x-atoms.tabs class="mb-4 ml-auto -mt-4" variant="pill">
            <x-atoms.tab
                variant="pill"
                href="?type=course"
                :active="$type !== 'formation'"
            >
                Tutoriels
            </x-atoms.tab>
                <x-atoms.tab
                    variant="pill"
                    href="?type=formation"
                    :active="$type === 'formation'"
                >
                    Formations
                </x-atoms.tab>
        </x-atoms.tabs>
        <div class="grid grid-fill-261 gap-6 grid-flow-row-dense">
            @foreach($items as $item)
                    @if($type === 'course')
                    <x-molecules.course-card :course="$item->progressable" :progress="$item->progress"/>
                    @else
                    <x-molecules.formation-card :formation="$item->progressable" :progress="$item->progress"/>
                    @endif
            @endforeach
        </div>
    </main>
@endsection
