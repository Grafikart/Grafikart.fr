<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Course\Technology;
use App\Http\Cms\Data\OptionItemData;
use App\Http\Cms\Data\Technology\TechnologyFormData;
use App\Http\Cms\Data\Technology\TechnologyRequestData;
use App\Http\Cms\Data\Technology\TechnologyRowData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class TechnologyController extends CmsController
{
    protected string $componentPath = 'technologies';

    protected string $model = Technology::class;

    protected string $rowData = TechnologyRowData::class;

    protected string $formData = TechnologyFormData::class;

    protected string $requestData = TechnologyRequestData::class;

    protected string $route = 'technologies';

    protected string $searchField = 'name';

    public function index(Request $request): Response|JsonResponse
    {
        // Handle API request for autocomplete
        if ($request->has('q') && ! $request->inertia()) {
            $search = $request->string('q');
            $technologies = Technology::query()
                ->where('name', 'ilike', "%{$search}%")
                ->orderBy('name')
                ->limit(10)
                ->get()
                ->map(fn ($tech) => new OptionItemData(
                    id: $tech->id,
                    name: $tech->name
                ));

            return response()->json($technologies);
        }

        $query = Technology::query()
            ->withCount('courses')
            ->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(TechnologyRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Technology $technology): Response
    {
        $technology->load(['requirements', 'deprecatedBy']);

        return $this->cmsEdit($technology);
    }

    public function update(Technology $technology, TechnologyRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $technology, data: $data);
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        return $this->cmsDestroy($technology, "La technologie {$technology->name} a été supprimée");
    }
}
