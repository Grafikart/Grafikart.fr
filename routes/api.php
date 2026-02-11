<?php

use App\Http\API\CourseFilterController;
use App\Http\API\ProgressController;
use App\Http\API\SearchController;
use App\Http\API\TwitchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [SearchController::class, 'index']);
Route::get('/courses/filters', [CourseFilterController::class, 'index']);
Route::post('/twitch/webhook', [TwitchController::class, 'webhook']);
Route::get('/courses/{course}/vtt', [\App\Http\API\CourseController::class, 'vtt'])->name('course.vtt');
Route::post('/stripe/webhook', [\App\Http\API\StripeWebhookController::class, 'webhook']);
Route::get('/premium/paypal/{orderId}', [\App\Http\API\PremiumController::class, 'paypal']);
Route::middleware('auth')->group(function () {
    Route::post('/courses/{course}/progress', [ProgressController::class, 'store']);
    Route::post('/premium/{plan}/stripe', [\App\Http\API\PremiumController::class, 'stripe'])
        ->whereNumber('plan')
        ->name('stripe.checkout');
    Route::post('/premium/paypal/{orderId}', [\App\Http\API\PremiumController::class, 'paypal']);
});
