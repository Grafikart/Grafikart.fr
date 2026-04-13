<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Course\Formation;
use App\Http\Cms\Data\Course\FormationFormData;
use App\Http\Cms\Data\Course\FormationRequestData;
use App\Http\Cms\Data\Course\FormationRowData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class FormationController extends CmsController
{
    protected string $componentPath = 'formations';

    protected string $model = Formation::class;

    protected string $rowData = FormationRowData::class;

    protected string $formData = FormationFormData::class;

    protected string $requestData = FormationRequestData::class;

    protected string $route = 'formations';

    public function index(Request $request): Response
    {
        $query = Formation::query()
            ->with('technologies')
            ->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(FormationRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Formation $formation): Response
    {
        $formation->load(['attachment', 'technologies']);

        return $this->cmsEdit($formation);
    }

    public function update(Formation $formation, FormationRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $formation, data: $data);
    }

    public function destroy(Formation $formation): RedirectResponse
    {
        return $this->cmsDestroy($formation, "La formation {$formation->title} a été supprimée");
    }
}
