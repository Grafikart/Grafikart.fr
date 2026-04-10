<?php

namespace App\Domains\Badge;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BadgeRepository
{
    /**
     * @return Collection<int, BadgeUnlockData>
     */
    public function forUser(int $userId): Collection
    {
        $badges = Badge::query()
            ->orderBy('position')
            ->get();

        $unlocks = DB::table('badge_user')
            ->where('user_id', $userId)
            ->pluck('badge_id');

        return $badges->map(fn (Badge $badge) => new BadgeUnlockData(
            name: $badge->name,
            description: $badge->description,
            theme: $badge->theme,
            image: $badge->image,
            unlocked: $unlocks->contains($badge->id),
        ));
    }
}
