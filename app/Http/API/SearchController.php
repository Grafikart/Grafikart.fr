<?php

namespace App\Http\API;

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
        $result = $this->search->search(q: $request->string('q'), limit: 6);

        $items = array_map(
            fn ($item) => new APISearchItem(
                title: $item->getTitle(),
                url: $item->getUrl(),
                type: $item->getType(),
            ),
            $result->getItems()
        );

        return new APISearchResponse(
            items: $items,
            hits: $result->getTotal(),
        );
    }
}
