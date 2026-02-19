<?php

namespace App\Policies;

use App\Domains\Revision\Revision;
use App\Models\User;

class RevisionPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Revision $revision): bool
    {
        return $revision->user_id === $user->id;
    }
}
