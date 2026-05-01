<?php

namespace App\Http\Cms;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
use App\Http\Cms\Data\SearchResultData;
use App\Http\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): mixed
    {
        $q = $request->string('q', '')->trim()->toString();
        $userQuery = null;
        if (str_contains($q, '@')) {
            $userQuery = User::query()->whereLike('email', "%{$q}%");
        }

        if (filter_var($q, FILTER_VALIDATE_IP)) {
            $userQuery = User::query()->where('last_login_ip', $q);
        }
        if ($userQuery) {
            return SearchResultData::collect(
                $userQuery->select('id', 'name')->limit(10)->get()
            );
        }

        $pos = strpos($q, ':');
        $search = $pos !== false ? substr($q, $pos + 1) : $q;
        $searchLike = "%{$search}%";

        if (str_starts_with($q, 'c:')) {
            return Technology::query()
                ->whereLike('name', $searchLike)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(fn (Technology $technology) => SearchResultData::from($technology));
        }

        $query = match (true) {
            str_starts_with($q, 'b:') => Post::query(),
            str_starts_with($q, 'f:') => Formation::query(),
            default => Course::query(),
        };

        return SearchResultData::collect($query->select(['id', 'title'])
            ->whereLike('title', $searchLike)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get());
    }
}
