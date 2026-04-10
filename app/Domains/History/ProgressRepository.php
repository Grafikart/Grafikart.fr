<?php

namespace App\Domains\History;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Retrieve last watched courses / formation for an User
 */
class ProgressRepository
{
    public function findItemsForUser(int $userId, string $type): LengthAwarePaginator
    {
        return Progress::where('user_id', $userId)
            ->where('progressable_type', $type)
            ->orderByDesc('updated_at')
            ->with('progressable')
            ->paginate(16);

    }
}
