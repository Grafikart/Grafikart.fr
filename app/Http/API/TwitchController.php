<?php

namespace App\Http\API;

use App\Domains\Live\LiveService;
use App\Http\Controller;
use App\Infrastructure\Twitch\TwitchAPI;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TwitchController extends Controller
{
    public function webhook(Request $request, TwitchAPI $api, LiveService $liveService): Response
    {
        /** @var array{challenge?: string, subscription?: array{type: string}} $json */
        $json = $request->json()->all();

        if (isset($json['challenge'])) {
            return response($json['challenge']);
        }

        if (! $api->validateSignature($request)) {
            return response('Invalid signature', Response::HTTP_FORBIDDEN);
        }

        if (($json['subscription']['type'] ?? null) === 'stream.online') {
            $liveService->startLive();
        }

        if (($json['subscription']['type'] ?? null) === 'stream.offline') {
            $liveService->stopLive();
        }

        return response('Event received');
    }
}
