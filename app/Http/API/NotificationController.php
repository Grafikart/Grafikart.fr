<?php

namespace App\Http\API;

use App\Domains\Notification\Models\Notification;
use App\Domains\Notification\NotificationData;
use App\Domains\Notification\NotificationService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController
{
    public function index(Request $request)
    {
        $limit = min($request->integer('limit', 15), 15);
        $user = $request->user();
        assert($user instanceof User);
        $notifications = Notification::query()
            ->latest()
            ->forUser($user)
            ->limit($limit)
            ->get();

        return NotificationData::collect($notifications);
    }

    public function read(Request $request, NotificationService $notification): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $notification->readAll($user);

        return new JsonResponse;
    }
}
