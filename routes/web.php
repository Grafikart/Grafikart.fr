<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;


Route::group(['prefix' => '/admin'], function () {
    Route::resource('blog_category', \App\Http\Admin\BlogCategoryController::class);
});

//Route::get('/', function () {
//    return Inertia::render('welcome', [
//        'canRegister' => Features::enabled(Features::registration()),
//    ]);
//})->name('home');

//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::get('dashboard', function () {
//        return Inertia::render('dashboard');
//    })->name('dashboard');
//});

//require __DIR__.'/settings.php';
