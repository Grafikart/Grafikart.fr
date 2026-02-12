<?php

namespace App\Http\Front;

use App\Domains\Notification\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController
{
    public function index(Request $request): View
    {
        $user = $request->user();
        assert($user instanceof User);
        $notifications = Notification::query()->forUser($user)->orderByDesc('created_at')->limit(15)->get();

        return view('pages.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
