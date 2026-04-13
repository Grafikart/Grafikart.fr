<?php

namespace App\Http\Cms;

use App\Domains\Badge\Badge;
use App\Domains\Cms\CmsController;
use App\Http\Cms\Data\Badge\BadgeFormData;
use App\Http\Cms\Data\Badge\BadgeRequestData;
use App\Http\Cms\Data\Badge\BadgeRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class BadgeController extends CmsController
{
    protected string $componentPath = 'badges';

    protected string $model = Badge::class;

    protected string $rowData = BadgeRowData::class;

    protected string $formData = BadgeFormData::class;

    protected string $requestData = BadgeRequestData::class;

    protected string $route = 'badges';

    protected string $searchField = 'name';

    public function index(): Response
    {
        $query = Badge::query()
            ->orderBy('position')
            ->orderBy('name');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(BadgeRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Badge $badge): Response
    {
        return $this->cmsEdit($badge);
    }

    public function update(Badge $badge, BadgeRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $badge, data: $data);
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        return $this->cmsDestroy($badge, "Le badge {$badge->name} a été supprimé");
    }
}
