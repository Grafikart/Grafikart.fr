<?php

namespace App\Domains\Cms;

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

abstract class CmsController
{

    protected string $componentPath = '';
    protected string $model = '';
    protected string $rowData = '';
    protected string $formData = '';
    protected string $requestData = '';
    protected string $route = '';
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'store' => ContentCreatedEvent::class,
        'destroy' => ContentDeletedEvent::class,
    ];

    protected function cmsIndex(?Builder $builder = null,  array $extra = []): Response
    {
        return Inertia::render(sprintf('%s/index', $this->componentPath), [
            'pagination' => ($this->rowData)::collect(
                ($builder ?? ($this->model)::query())->paginate(15)),
            ...$extra
        ]);
    }

    protected function cmsEdit(object $model): Response
    {
        assert($model instanceof $this->model);
        return Inertia::render(sprintf('%s/form', $this->componentPath), [
            'item' => ($this->formData)::from($model),
        ]);
    }

    protected function cmsUpdate(object $model, object $data): RedirectResponse
    {
        assert($model instanceof $this->model);
        assert($data instanceof $this->formData);
        $model->fill([
            'slug' => $data->slug,
            'name' => $data->name
        ]);
        $model->save();
        event(new ($this->events['update'])($model));
        return to_route(sprintf('%s.index', $this->route))->with('success', 'Le contenu a bien été modifié');
    }

    protected function cmsCreate(array $extra = []): Response
    {
        return Inertia::render(sprintf('%s/form', $this->componentPath), [
            'item' => new ($this->formData)(),
            ...$extra
        ]);
    }

    public function cmsStore(object $data): RedirectResponse
    {
        assert($data instanceof DataToModel);
        $model = new ($this->model)();
        $data->toModel($model);
        $model->save();
        event(new ($this->events['store'])($model));;
        return to_route(sprintf('%s.index', $this->route))->with('success', 'Le contenu a bien été créé');
    }

    public function cmsDestroy(object $model): RedirectResponse
    {
        assert($model instanceof $this->model);
        $model->delete();
        event(new ($this->events['destroy'])($model));
        return to_route(sprintf('%s.index', $this->route))->with('success', 'Le contenu a bien été supprimé');
    }

}
