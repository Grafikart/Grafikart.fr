@extends('front', ['class' => 'has-sidebar'])

@section('title', ($type === 'course' ? 'Tous les tutoriesl' : 'Toutes les formations') . ($page > 1 ? ', page ' . $page : ''))

@section('body')
    <div class="ml-(--sidebar-width)" style="--sidebar-width: 300px;">
        <main class="max-w-container mx-auto">
            <div class="grid grid-fill-261 gap-6 grid-flow-row-dense">
                @if($show_title)
                    @if($page === 1)
                        <div class="sm:col-span-2 self-center">
                            <h1 class="text-6xl font-bold font-serif mb-4 text-foreground-title">
                                @if($type === 'course')
                                    Tous les <span class="text-primary">tutoriels</span>
                                @else
                                    Toutes les <span class="text-primary">formations</span>
                                @endif
                            </h1>
                            <p class="text-xl text-balance">
                                @if($type === 'course')
                                    Envie d'apprendre de nouvelles choses et maitriser de nouvelles technologies ?
                                    Alors vous êtes sur le bon chemin...
                                @else
                                    Découvrez une technologie spécifique à travers une série de vidéo qui vous guidera
                                    dans votre apprentissage
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="sm:col-span-2 xl:col-start-3 self-center text-end">
                            <h1 class="text-6xl font-bold font-serif mb-2  text-foreground-title">
                                @if($type === 'course')
                                    Tous les <span class="text-primary">tutoriels</span>
                                @else
                                    Toutes les <span class="text-primary">formations</span>
                                @endif
                                <span class="hidden">Page {{ $page }}</span>
                            </h1>
                            <p class="text-xl text-balance text-muted">
                                Page {{ $page }}
                            </p>
                        </div>
                    @endif
                @endif
                @foreach($items as $item)
                    @if($type === 'formation')
                        <x-molecules.formation-card :formation="$item"/>
                    @else
                        <x-molecules.course-card :course="$item"/>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                {{ $items->links() }}
            </div>
        </main>

        <x-molecules.drawer side="left" class="w-75">
            <course-filters></course-filters>
        </x-molecules.drawer>
    </div>

@endsection
