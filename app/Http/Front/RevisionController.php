<?php

namespace App\Http\Front;

use App\Domains\Revision\Revision;
use App\Domains\Revision\Revisionable;
use App\Domains\Revision\RevisionService;
use App\Http\Controller;
use App\Http\Front\Data\RevisionData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RevisionController extends Controller
{
    /**
     * @return Revisionable&Model
     */
    private function resolveTarget(string $type, int $id): Revisionable
    {
        $modelClass = Relation::getMorphedModel($type);

        if (! $modelClass || ! is_a($modelClass, Revisionable::class, true)) {
            throw new NotFoundHttpException;
        }

        $model = $modelClass::findOrFail($id);
        assert($model instanceof Revisionable);

        return $model;
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $revisions = Revision::query()
            ->where('user_id', $user->id)
            ->with('revisionable')
            ->latest()
            ->paginate(20);

        return view('revisions.index', [
            'revisions' => $revisions,
            'user' => $user,
        ]);
    }

    public function edit(string $type, int $id, RevisionService $service): View
    {
        Gate::authorize('create', Revision::class);

        $target = $this->resolveTarget($type, $id);

        return view('revisions.edit', [
            'type' => $type,
            'id' => $id,
            'target' => $target,
            'content' => $service->getContentForUser($target),
        ]);
    }

    public function update(string $type, int $id, RevisionData $data, RevisionService $service): RedirectResponse
    {
        Gate::authorize('create', Revision::class);

        $target = $this->resolveTarget($type, $id);

        $service->sendRevision($target, $data->content);

        return to_route('revisions.index')->with('success', 'Votre proposition de modification a bien été envoyée.');
    }

    public function delete(Revision $revision): RedirectResponse
    {
        Gate::authorize('delete', $revision);

        $revision->delete();

        return back()->with('success', 'La révision a bien été supprimée.');
    }
}
