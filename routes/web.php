<?php

use App\Http\Cms\BlogCategoryController;
use App\Http\Cms\PlanController;
use App\Http\Cms\TechnologyController;
use App\Http\Cms\TransactionController;
use App\Http\Cms\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/cms', 'as' => 'cms.'], function () {
    Route::resource('blog_categories', BlogCategoryController::class);
    Route::resource('plans', PlanController::class)->except(['edit', 'create']);
    Route::resource('technologies', TechnologyController::class);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'destroy']);
});

// Route::get('/', function () {
//    return Inertia::render('welcome', [
//        'canRegister' => Features::enabled(Features::registration()),
//    ]);
// })->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//    Route::get('dashboard', function () {
//        return Inertia::render('dashboard');
//    })->name('dashboard');
// });

// require __DIR__.'/settings.php';
