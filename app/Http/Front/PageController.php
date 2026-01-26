<?php

namespace App\Http\Front;

use App\Http\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function ui(): View
    {
        return view('pages.ui');
    }
}
