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

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function about(): View
    {
        return view('pages.about');
    }
}
