<?php

use Illuminate\Support\Facades\Route;

$slug = '[0-9]+';

// Public routes
Route::get('/ui', [\App\Http\Front\PageController::class, 'ui']);
Route::get('/media/resize/{width}/{height}/{path}', [\App\Http\Front\ImageController::class, 'resize'])
    ->where('path', '.*')
    ->whereNumber(['width', 'height'])
    ->name('image.resize');

// Course
Route::get('/cursus', [\App\Http\Front\PathController::class, 'index'])->name('paths.index');
Route::get('/cursus/{slug}-{path}', [\App\Http\Front\PathController::class, 'show'])
    ->name('paths.show')
    ->whereNumber('path')
    ->middleware(\App\Http\Middleware\RedirectIfSlugMismatch::class.':path');
Route::get('/cursus/{node}', [\App\Http\Front\PathController::class, 'node'])->name('paths.node');
Route::get('/tutoriels', [\App\Http\Front\CourseController::class, 'index'])->name('courses.index');
Route::get('/tutoriels/{slug}-{course}', [\App\Http\Front\CourseController::class, 'show'])
    ->whereNumber('course')
    ->middleware(\App\Http\Middleware\RedirectIfSlugMismatch::class.':course')
    ->name('courses.show');
Route::get('/formations', [\App\Http\Front\CourseController::class, 'index'])->name('formations.index');
Route::get('/formations/{formation:slug}', [\App\Http\Front\CourseController::class, 'show'])
    ->name('formations.show');
Route::get('/recherche', [\App\Http\Front\SearchController::class, 'index']);

// BLOG
Route::group(['prefix' => '/blog', 'as' => 'blog.'], function () {
    Route::get('/', [\App\Http\Front\BlogController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [\App\Http\Front\BlogController::class, 'index'])->name('category');
    Route::get('/{post:slug}', [\App\Http\Front\BlogController::class, 'show'])->name('show');
});

// Admin routes
Route::group(['prefix' => '/cms', 'as' => 'cms.'], function () {
    Route::get('/', function () {
        return redirect('/cms/dashboard');
    });
    Route::get('dashboard', \App\Http\Cms\DashboardController::class)->name('dashboard');
    Route::resource('blog_categories', \App\Http\Cms\BlogCategoryController::class);
    Route::resource('comments', \App\Http\Cms\CommentController::class)->only(['index', 'update', 'destroy']);
    Route::resource('courses', \App\Http\Cms\CourseController::class);
    Route::resource('formations', \App\Http\Cms\FormationController::class)->except(['show']);
    Route::resource('paths', \App\Http\Cms\PathController::class)->except(['show']);
    Route::resource('posts', \App\Http\Cms\PostController::class)->except(['show']);
    Route::resource('plans', \App\Http\Cms\PlanController::class)->except(['edit', 'create']);
    Route::resource('technologies', \App\Http\Cms\TechnologyController::class)->except(['show']);
    Route::resource('users', \App\Http\Cms\UserController::class)->only(['index', 'destroy']);
    Route::resource('transactions', \App\Http\Cms\TransactionController::class)->only(['index', 'destroy']);
    Route::resource('settings', \App\Http\Cms\SettingsController::class)->only(['index', 'store']);
    Route::get('search', [\App\Http\Cms\SearchController::class, 'search'])->name('search');

    // Attachments (JSON API)
    Route::get('attachments/folders', [\App\Http\Cms\AttachmentController::class, 'folders'])->name('attachments.folders');
    Route::resource('attachments', \App\Http\Cms\AttachmentController::class)->only(['store', 'destroy', 'index']);
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
