@extends('front')

@section('body')
    <div class="container mx-auto py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="sm:col-span-2 self-center">
                <h1 class="text-6xl font-bold font-serif mb-4">
                    Tous les <span class="text-primary">tutoriels</span>
                </h1>
                <p class="text-xl text-balance">
                    Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
                    Alors vous êtes sur le bon chemin...
                </p>
            </div>
            @foreach($courses as $course)
                <x-molecules.course-card :course="$course" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </div>
@endsection
