<?php

namespace App\Domains\Revision;

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
}
