<?php

namespace App\View\Components;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
use App\Helpers\UrlGenerator;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public function __construct(
        private UrlGenerator $urlGenerator,
        public Model $model
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumbs', [
            'items' => match (true) {
                $this->model instanceof Course => $this->course($this->model),
                $this->model instanceof Formation => $this->formation($this->model),
                default => [],
            }
        ]);
    }

    private function course(Course $course): array
    {
        $items = collect([['label' => 'Tutoriels', 'url' => route('courses.index')]]);
        $categories = $course->mainTechnologies->map(fn(Technology $technology) => ['label' => $technology->name, 'url' => route('technologies.show', ['technology' => $technology->slug])]);
        if ($categories->count() > 0) {
            $items->push($categories);
        }
        $formation = $course->formation;
        if ($formation) {
            $items->push(['label' => $formation->title, 'url' => $this->urlGenerator->url($formation)]);
        }
        $items->push([
            'label' => $course->title,
            'url' => $this->urlGenerator->url($course)
        ]);
        return $items->all();
    }

    private function formation(Formation $formation): array
    {
        $items = collect([
            ['label' => 'Formation', 'url' => route('formations.index')]
        ]);
        $categories = $formation->mainTechnologies->map(fn(Technology $technology) => ['label' => $technology->name, 'url' => route('technologies.show', ['technology' => $technology->slug])]);
        if ($categories->count() > 0) {
            $items->push($categories);
        }
        return $items->all();
    }

}
