<?php

namespace App\Http\API;

use App\Domains\Course\Technology;
use App\Http\API\Data\APISearchItem;
use App\Http\API\Data\APISearchResponse;
use App\Http\Controller;
use App\Infrastructure\Search\Contracts\SearchInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private readonly SearchInterface $search) {}

    public function index(Request $request): APISearchResponse
    {
        // Find technology matching the query
        $q = trim($request->string('q'));

        // No search, no results
        if (empty($q)) {
            return new APISearchResponse(
                items: [],
                hits: 0
            );
        }

        /** @var APISearchItem[] $items */
        $items = [];

        // Find matching technologies
        $technologies = Technology::query()
            ->published()
            ->whereLike('name', '%'.$q.'%')
            ->limit(10)
            ->get();
        foreach ($technologies as $technology) {
            $items[] = new APISearchItem(
                title: $technology->name,
                url: app_url($technology),
                type: 'Outil',
            );
        }

        // Search the site
        $search = $this->search->search(q: $request->string('q'), limit: 6);
        foreach ($search->getItems() as $item) {
            $items[] = new APISearchItem(
                title: $item->getTitle(),
                url: $item->getUrl(),
                type: $item->getType(),
            );
        }

        return new APISearchResponse(
            items: $items,
            hits: $search->getTotal(),
        );
    }
}
