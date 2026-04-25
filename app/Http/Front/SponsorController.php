<?php

namespace App\Http\Front;

use App\Domains\Sponsorship\Sponsor;
use App\Domains\Sponsorship\SponsorType;
use App\Http\Controller;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(): View
    {
        $sponsors = Sponsor::latest()->limit(10)->get();

        return view('pages.sponsors', [
            'sponsors' => $sponsors->where('type', SponsorType::Sponsor),
            'affiliates' => $sponsors->where('type', SponsorType::Affiliation),
        ]);
    }
}
