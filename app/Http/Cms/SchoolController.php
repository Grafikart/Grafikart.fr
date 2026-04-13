<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\School\School;
use App\Http\Cms\Data\OptionItemData;
use App\Http\Cms\Data\School\SchoolFormData;
use App\Http\Cms\Data\School\SchoolRequestData;
use App\Http\Cms\Data\School\SchoolRowData;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class SchoolController extends CmsController
{
    protected string $componentPath = 'schools';

    protected string $model = School::class;

    protected string $rowData = SchoolRowData::class;

    protected string $formData = SchoolFormData::class;

    protected string $requestData = SchoolRequestData::class;

    protected string $route = 'schools';

    protected string $searchField = 'name';

    public function index(): Response
    {
        $query = School::query()->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(SchoolRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(School $school): Response
    {
        $school->load('students');

        return $this->cmsEdit($school);
    }

    public function update(School $school, SchoolRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $school, data: $data);
    }

    public function destroy(School $school): RedirectResponse
    {
        return $this->cmsDestroy($school, "L'école {$school->name} a été supprimée");
    }

    /**
     * @return array<OptionItemData>
     */
    private function getOwners(?School $school = null): array
    {
        $usedOwnerIds = School::query()
            ->when($school !== null, fn (Builder $query) => $query->whereKeyNot($school->id))
            ->pluck('user_id');

        return OptionItemData::collect(
            User::query()
                ->select(['id', 'name'])
                ->when($usedOwnerIds->isNotEmpty(), fn (Builder $query) => $query->whereNotIn('id', $usedOwnerIds))
                ->when($school?->user_id !== null, fn (Builder $query) => $query->orWhereKey($school->user_id))
                ->orderBy('name')
                ->get()
        )->toArray();
    }
}
