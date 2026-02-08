<?php

use App\Http\API\ProgressController;
use App\Http\API\SearchController;
use App\Http\API\TwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [SearchController::class, 'index']);
Route::post('/twitch/webhook', [TwitchController::class, 'webhook']);

Route::middleware('auth')->group(function () {
    Route::post('/progress/{course}', [ProgressController::class, 'store']);
});
