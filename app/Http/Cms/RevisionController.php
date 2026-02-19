<?php

namespace App\Http\Cms;

use App\Domains\Revision\Revision;
use App\Domains\Revision\RevisionService;
use App\Domains\Revision\RevisionStatus;
use App\Http\Cms\Data\Revision\RevisionRowData;
use App\Http\Cms\Data\Revision\RevisionShowData;
use App\Http\Cms\Data\Revision\RevisionUpdateData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class RevisionController
{
    public function __construct(private RevisionService $service) {}

    public function index(Request $request): Response
    {
        $query = Revision::query()
            ->with(['user', 'revisionable'])
            ->orderByDesc('created_at');

        $state = $request->query('state', 'pending');
        if ($state === 'pending') {
            $query->pending();
        } elseif ($state === 'accepted') {
            $query->where('state', RevisionStatus::Accepted);
        } elseif ($state === 'rejected') {
            $query->where('state', RevisionStatus::Rejected);
        }

        return Inertia::render('revisions/index', [
            'pagination' => RevisionRowData::collect($query->paginate(20)),
            'state' => $state,
        ]);
    }

    public function show(Revision $revision): Response
    {
        $revision->load(['user', 'revisionable']);

        return Inertia::render('revisions/show', [
            'revision' => RevisionShowData::fromModel($revision),
        ]);
    }

    public function update(Revision $revision, RevisionUpdateData $data): RedirectResponse
    {
        if ($data->state === RevisionStatus::Accepted) {
            $this->service->accept($revision, $data->comment);
        } else {
            $this->service->reject($revision, $data->comment);
        }

        $message = $data->state === RevisionStatus::Accepted ? 'Révision acceptée.' : 'Révision rejetée.';

        return redirect()->route('cms.revisions.index')->with('success', $message);
    }
}
