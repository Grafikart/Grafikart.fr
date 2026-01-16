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
 * Contrôleur de base pour les ressources CMS.
 *
 * Fournit les méthodes CRUD standards pour gérer les contenus du CMS.
 * Les contrôleurs enfants doivent définir les propriétés de configuration
 * et peuvent surcharger les méthodes pour personnaliser le comportement.
 *
 * @template TModel of Model
 * @template TRowData of Data
 * @template TFormData of Data
 */
abstract class CmsController
{
    // @var string Chemin vers les composants Inertia (ex: 'plans', 'blog/categories')
    protected string $componentPath = '';

    // @var class-string<TModel> Classe du modèle Eloquent
    protected string $model = '';

    // @var class-string<TRowData> Classe Data pour la liste (index)
    protected string $rowData = '';

    // @var class-string<TFormData> Classe Data pour le formulaire (edit/create)
    protected string $formData = '';

    // @var class-string<Data&DataToModel> Classe Data pour la validation des requêtes
    protected string $requestData = '';

    // @var string Nom de la route pour les redirections (ex: 'plans', 'blog_categories')
    protected string $route = '';

    // @var array{update: class-string, store: class-string, destroy: class-string} Événements à dispatcher
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'store' => ContentCreatedEvent::class,
        'destroy' => ContentDeletedEvent::class,
    ];

    protected function cmsIndex(?Builder $builder = null, array $extra = []): Response
    {
        return Inertia::render(sprintf('%s/index', $this->componentPath), [
            'pagination' => ($this->rowData)::collect(
                ($builder ?? ($this->model)::query())->paginate(15)),
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
        assert($data instanceof $this->formData);
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

    public function cmsDestroy(Model $model): RedirectResponse
    {
        assert($model instanceof $this->model);
        $model->delete();
        event(new ($this->events['destroy'])($model));

        return to_route(sprintf('cms.%s.index', $this->route))->with('success', 'Le contenu a bien été supprimé');
    }
}
