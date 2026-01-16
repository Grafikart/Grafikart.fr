<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Premium\Models\Plan;
use App\Http\Cms\Data\Premium\PlanData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class PlanController extends CmsController
{
    protected string $componentPath = 'plans';

    protected string $model = Plan::class;

    protected string $rowData = PlanData::class;

    protected string $formData = PlanData::class;

    protected string $requestData = PlanData::class;

    protected string $route = 'plans';

    public function index(): Response
    {
        return $this->cmsIndex();
    }

    public function update(Plan $plan, PlanData $data): RedirectResponse
    {
        return $this->cmsUpdate($plan, $data);
    }

    public function store(PlanData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        return $this->cmsDestroy($plan);
    }
}
