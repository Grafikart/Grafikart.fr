<?php

namespace App\Domains\Cms;

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\Data;

/**
 * Base controller for CMS resources.
 *
 * Provides standard CRUD methods for managing CMS content.
 * Child controllers must define configuration properties
 * and can override methods to customize behavior.
 *
 * @template TModel of Model
 * @template TRowData of Data
 * @template TFormData of Data
 */
abstract class CmsController
{
    // @var string Path to Inertia components (e.g., 'plans', 'blog/categories')
    protected string $componentPath = '';

    // @var class-string<TModel> Eloquent model class
    protected string $model = '';

    // @var class-string<TRowData> Data class for listing (index)
    protected string $rowData = '';

    // @var class-string<TFormData> Data class for the form (edit/create)
    protected string $formData = '';

    // @var class-string<Data&DataToModel> Data class for request validation
    protected string $requestData = '';

    // @var string Route name for redirections (e.g., 'plans', 'blog_categories')
    protected string $route = '';

    // @var array{update: class-string, store: class-string, destroy: class-string} Events to dispatch
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'store' => ContentCreatedEvent::class,
        'destroy' => ContentDeletedEvent::class,
    ];

    protected function cmsIndex(?Builder $query = null, array $extra = []): Response
    {
        return Inertia::render(sprintf('%s/index', $this->componentPath), [
            'pagination' => ($this->rowData)::collect(
                ($query ?? ($this->model)::query())->paginate(15)),
            ...$extra,
        ]);
    }

    protected function cmsEdit(Model $model, array $extra = []): Response
    {
        assert($model instanceof $this->model);

        return Inertia::render(sprintf('%s/form', $this->componentPath), [
            'item' => ($this->formData)::from($model),
            ...$extra,
        ]);
    }

    protected function cmsUpdate(Model $model, DataToModel $data): RedirectResponse
    {
        assert($model instanceof $this->model);
        assert($data instanceof $this->requestData);
        $data->toModel($model);
        $model->save();
        event(new ($this->events['update'])($model));

        return to_route(sprintf('cms.%s.index', $this->route))->with('success', 'Le contenu a bien été modifié');
    }

    protected function cmsCreate(array $extra = []): Response
    {
        return Inertia::render(sprintf('%s/form', $this->componentPath), [
            'item' => new ($this->formData)(),
            ...$extra,
        ]);
    }

    public function cmsStore(DataToModel $data): RedirectResponse
    {
        assert($data instanceof DataToModel);
        $model = new ($this->model)();
        $data->toModel($model);
        $model->save();
        event(new ($this->events['store'])($model));

        return to_route(sprintf('cms.%s.index', $this->route))->with('success', 'Le contenu a bien été créé');
    }

    public function cmsDestroy(Model $model, ?string $message = null): RedirectResponse
    {
        assert($model instanceof $this->model);
        $model->delete();
        event(new ($this->events['destroy'])($model));

        return to_route(sprintf('cms.%s.index', $this->route))->with('success', $message ?? 'Le contenu a bien été supprimé');
    }
}
