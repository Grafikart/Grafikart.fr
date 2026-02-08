@extends('front')

@section('body')
    <div class="container mx-auto">
        <div class="grid grid-fill-261 gap-6 grid-flow-row-dense">
            @if($page === 1)
            <div class="sm:col-span-2 self-center">
                <h1 class="text-6xl font-bold font-serif mb-4 text-foreground-title">
                    Tous les <span class="text-primary">tutoriels</span>
                </h1>
                <p class="text-xl text-balance">
                    Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
                    Alors vous êtes sur le bon chemin...
                </p>
            </div>
            @else
                <div class="sm:col-span-2 xl:col-start-3 self-center text-end">
                    <h1 class="text-6xl font-bold font-serif mb-2  text-foreground-title">
                        Tous les <span class="text-primary">tutoriels</span> <span class="hidden">Page {{ $page }}</span>
                    </h1>
                    <p class="text-xl text-balance text-muted">
                        Page {{ $page }}
                    </p>
                </div>
            @endif
            @foreach($courses as $course)
                <x-molecules.course-card :course="$course" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </div>
@endsection
