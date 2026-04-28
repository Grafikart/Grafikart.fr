<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Course\Path;
use App\Http\Cms\Data\Course\PathFormData;
use App\Http\Cms\Data\Course\PathRequestData;
use App\Http\Cms\Data\Course\PathRowData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class PathController extends CmsController
{
    protected string $componentPath = 'paths';

    protected string $model = Path::class;

    protected string $rowData = PathRowData::class;

    protected string $formData = PathFormData::class;

    protected string $requestData = PathRequestData::class;

    protected string $route = 'paths';

    public function index(Request $request): Response
    {
        $query = Path::query()->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(Request $request): Response
    {
        $clone = $request->query->getInt('clone', 0);
        if ($clone) {
            $path = clone Path::findOrFail($clone);
            $path->load('nodes', 'nodes.content', 'nodes.parents');
            $path->setAttribute('id', null);

            return $this->cmsCreate(['item' => PathFormData::from($path)]);
        }

        return $this->cmsCreate();
    }

    public function store(PathRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Path $path): Response
    {
        $path->load('nodes', 'nodes.content', 'nodes.parents');

        return $this->cmsEdit($path);
    }

    public function update(Path $path, PathRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $path, data: $data);
    }

    public function destroy(Path $path): RedirectResponse
    {
        return $this->cmsDestroy($path, "Le parcours {$path->title} a été supprimé");
    }
}
