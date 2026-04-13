<?php

namespace App\Http\Front;

use App\Domains\Premium\Models\Plan;
use App\Http\Controller;
use App\Http\Front\Data\StudentProgressData;
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

    public function premium(): View
    {
        return view('pages.premium', [
            'plans' => Plan::all(),
        ]);
    }

    public function school(): View
    {
        // Fake data for the formations
        $formations = [
            new StudentProgressData(
                title: 'Apprendre React',
                icon: '/uploads/icons/react.svg',
                chapters: 31,
                completedChapters: 15,
            ),
            new StudentProgressData(
                title: 'Comprendre Git',
                icon: '/uploads/icons/git.svg',
                chapters: 18,
                completedChapters: 3,
            ),
            new StudentProgressData(
                title: 'Apprendre JavaScript',
                icon: '/uploads/icons/javascript.svg',
                chapters: 56,
                completedChapters: 56,
            ),
            new StudentProgressData(
                title: "L'algorithmique",
                icon: '/uploads/icons/algorithmique.svg',
                chapters: 10,
                completedChapters: 10,
            ),
            new StudentProgressData(
                title: 'Découverte du CSS',
                icon: '/uploads/icons/css.svg',
                chapters: 37,
                completedChapters: 33,
            ),
            new StudentProgressData(
                title: "Comprendre l'HTML",
                icon: '/uploads/icons/html.svg',
                chapters: 10,
                completedChapters: 10,
            ),
        ];

        return view('pages.school', [
            'formations' => $formations,
        ]);
    }
}
