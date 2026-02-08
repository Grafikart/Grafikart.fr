@extends('front')

@section('title', 'Page introuvable')

@section('body')
    @cache('e404')
    @php
        $courses = \App\Domains\Course\Course::query()
            ->published()
            ->inRandomOrder()
            ->limit(4)
            ->get();
    @endphp

    <div class="container pb-10 bg-background-light">
        <h2 class="text-page-title mb-4">Page introuvable</h2>
        <p class="text-xl text-muted max-w-2xl mx-auto">
            Cette page s'est perdue dans le code... ou peut-être qu'elle n'a jamais existé...<br/>
        </p>
    </div>

    <div class="bg-background border-t pt-15 container">
        <h2 class="text-3xl font-bold font-serif mb-6 text-foreground-title">
            Profitez d'être perdu pour apprendre de nouvelles choses
        </h2>
        <div class="grid grid-fill-261 gap-6">
            @foreach($courses as $course)
                <x-molecules.course-card :course="$course"/>
            @endforeach
        </div>
    </div>
    @endcache
@endsection
