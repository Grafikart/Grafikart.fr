<?php

namespace App\Http\Front;

use App\Domains\Live\LiveService;
use App\Http\Controller;
use Illuminate\View\View;

class LiveController extends Controller
{
    public function show(LiveService $liveService): View
    {
        return view('pages.live', [
            'isLive' => $liveService->isLiveRunning(),
            'liveAt' => $liveService->getNextLiveDate(),
        ]);
    }
}
