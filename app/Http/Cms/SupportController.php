<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Support\SupportQuestion;
use App\Http\Cms\Data\Support\SupportQuestionFormData;
use App\Http\Cms\Data\Support\SupportQuestionRequestData;
use App\Http\Cms\Data\Support\SupportQuestionRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class SupportController extends CmsController
{
    protected string $componentPath = 'support';

    protected string $model = SupportQuestion::class;

    protected string $rowData = SupportQuestionRowData::class;

    protected string $formData = SupportQuestionFormData::class;

    protected string $requestData = SupportQuestionRequestData::class;

    protected string $route = 'support';

    public function index(): Response
    {
        $query = SupportQuestion::query()
            ->with(['course:id,title'])
            ->orderByDesc('id');

        return $this->cmsIndex(query: $query);
    }

    public function edit(SupportQuestion $support): Response
    {
        $support->loadMissing(['course:id,title']);

        return $this->cmsEdit($support);
    }

    public function update(SupportQuestion $support, SupportQuestionRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $support, data: $data);
    }

    public function destroy(SupportQuestion $support): RedirectResponse
    {
        return $this->cmsDestroy($support, 'La question de support a bien été supprimée');
    }
}
