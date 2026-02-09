<?php

use App\Http\API\ProgressController;
use App\Http\API\SearchController;
use App\Http\API\TwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [SearchController::class, 'index']);
Route::post('/twitch/webhook', [TwitchController::class, 'webhook']);
Route::get('/courses/{course}/vtt', [\App\Http\API\CourseController::class, 'vtt'])->name('course.vtt');

Route::middleware('auth')->group(function () {
    Route::post('/courses/{course}/progress', [ProgressController::class, 'store']);
});
