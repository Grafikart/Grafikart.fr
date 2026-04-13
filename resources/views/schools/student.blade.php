@extends('front')

@section('title', 'Etudiant ' . $name)

@section('body')

    <div class="container pb-10 bg-background-light">
        <h1 class="text-page-title mb-2">{{ $name }}</h1>

        <p class="text-muted gap-1 items-center flex">
            {{ $email }} -
            <a href="{{ route('schools.show') }}" class="hover:underline">
                {{ $school }}
            </a>
        </p>
    </div>

    <div class="border-t bg-background pt-10 pb-20 container">

        <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2 mb-4">
            <x-lucide-graduation-cap class="size-6 text-primary"/>
            Progression de la formation
        </h2>

        <div class="grid grid-fill-275">
            @foreach($items as $item)
                <x-molecules.student-progress-card :item="$item"/>
            @endforeach
        </div>


    </div>

@endsection
