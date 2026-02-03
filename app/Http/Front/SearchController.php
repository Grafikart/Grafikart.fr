<?php

namespace App\Http\Front;

use App\Http\Controller;
use App\Infrastructure\Search\Contracts\SearchInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const PER_PAGE = 10;

    public function __construct(private readonly SearchInterface $search) {}

    public function index(Request $request): View
    {
        $q = $request->query('q', '');
        $page = $request->integer('page', 1);

        if (empty($q)) {
            return view('search.index', [
                'q' => $q,
                'total' => 0,
                'items' => new LengthAwarePaginator(items: [], total: 0, perPage: self::PER_PAGE),
            ]);
        }

        $results = $this->search->search($request->string('q'), [], self::PER_PAGE, $page);

        $items = new LengthAwarePaginator(
            items: $results->getItems(),
            total: $results->getTotal(),
            perPage: self::PER_PAGE,
            currentPage: $page,
            options: ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('search.index', [
            'q' => $q,
            'total' => $results->getTotal(),
            'items' => $items,
        ]);
    }
}
