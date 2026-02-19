<?php

namespace App\Domains\Revision;

use App\Domains\Revision\Event\AcceptedRevisionEvent;
use App\Domains\Revision\Event\RejectedRevisionEvent;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;

class RevisionService
{
    public function __construct(private readonly Guard $guard) {}

    public function sendRevision(Revisionable $target, string $content): Revision
    {
        $userId = $this->guard->id();
        assert($userId, "Impossible de récupérer l'id de l'utilisateur connecté");
        $revision = new Revision;
        $revision->user_id = $userId;
        $revision->content = $content;
        $revision->state = RevisionStatus::Pending;
        $revision->revisionable()->associate($target);
        $revision->save();

        return $revision;
    }

    /**
     * Returns the content of the user's last pending revision for that target if it exists,
     * otherwise returns the target's current content.
     */
    public function getContentForUser(Revisionable $target): string
    {
        $userId = $this->guard->id();

        assert($target instanceof Model && $target->hasAttribute('content'), 'Impossible de récupérer le contenu associé');

        if (! $userId) {
            return $target->content;
        }

        $pendingRevision = Revision::query()
            ->whereMorphedTo('revisionable', $target)
            ->where('user_id', $userId)
            ->pending()
            ->latest()
            ->first();

        return $pendingRevision?->content ?? $target->content;
    }

    public function accept(Revision $revision, ?string $comment = null): void
    {
        if ($revision->revisionable) {
            $revision->revisionable->update(['content' => $revision->content]);
        }

        $revision->update([
            'state' => RevisionStatus::Accepted,
            'comment' => $comment,
        ]);

        event(new AcceptedRevisionEvent($revision));
    }

    public function reject(Revision $revision, ?string $comment = null): void
    {
        $revision->update([
            'state' => RevisionStatus::Rejected,
            'comment' => $comment,
        ]);

        event(new RejectedRevisionEvent($revision));
    }
}
