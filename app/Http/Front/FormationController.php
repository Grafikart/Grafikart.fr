<?php

namespace App\Http\Front;

use App\Domains\Course\Formation;
use App\Http\Front\Data\ContentFilterData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormationController
{
    public function index(ContentFilterData $filter): View
    {
        $query = Formation::query()
            ->where('online', true)
            ->whereNull('deprecated_by_id')
            ->with('mainTechnologies', 'courses')
            ->orderByDesc('created_at');

        if ($filter->technology) {
            $query->whereHas('mainTechnologies', fn ($q) => $q->where('slug', $filter->technology));
        }

        $items = $query->paginate($filter->perPage())->withQueryString();

        return view('courses.index', [
            'items' => $items,
            'page' => $filter->page,
            'type' => 'formation',
            'show_title' => ! $filter->isActive(),
        ]);
    }

    public function show(Request $request)
    {
        throw new \Error('Implement this');
    }
}
