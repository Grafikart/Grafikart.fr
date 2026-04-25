<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Sponsorship\Sponsor;
use App\Domains\Sponsorship\SponsorType;
use App\Http\Cms\Data\Sponsor\SponsorFormData;
use App\Http\Cms\Data\Sponsor\SponsorRequestData;
use App\Http\Cms\Data\Sponsor\SponsorRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class SponsorController extends CmsController
{
    protected string $componentPath = 'sponsors';

    protected string $model = Sponsor::class;

    protected string $rowData = SponsorRowData::class;

    protected string $formData = SponsorFormData::class;

    protected string $requestData = SponsorRequestData::class;

    protected string $route = 'sponsors';

    protected string $searchField = 'name';

    public function index(): Response
    {
        $query = Sponsor::query()->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate(['types' => $this->getTypes()]);
    }

    public function store(SponsorRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Sponsor $sponsor): Response
    {
        return $this->cmsEdit($sponsor, ['types' => $this->getTypes()]);
    }

    /** @return array<int, array{value: string, label: string}> */
    private function getTypes(): array
    {
        return array_map(
            fn (SponsorType $type) => ['value' => $type->value, 'label' => $type->label()],
            SponsorType::cases()
        );
    }

    public function update(Sponsor $sponsor, SponsorRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $sponsor, data: $data);
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        return $this->cmsDestroy($sponsor, "Le sponsor {$sponsor->name} a été supprimé");
    }
}
