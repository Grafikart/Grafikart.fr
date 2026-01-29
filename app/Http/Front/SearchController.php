<?php

namespace App\Http\Front;

use App\Http\Controller;
use App\Infrastructure\Search\Contracts\SearchInterface;

class SearchController extends Controller
{
    public function __construct(private readonly SearchInterface $search) {}

    public function index(\Illuminate\Http\Request $request)
    {
        dd($this->search->search($request->string('q')));
    }
}
