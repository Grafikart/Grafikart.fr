<?php

use App\Http\Cms\AttachmentController;
use App\Http\Cms\BlogCategoryController;
use App\Http\Cms\CommentController;
use App\Http\Cms\CourseController;
use App\Http\Cms\DashboardController;
use App\Http\Cms\FormationController;
use App\Http\Cms\PathController;
use App\Http\Cms\PlanController;
use App\Http\Cms\PostController;
use App\Http\Cms\SettingsController;
use App\Http\Cms\TechnologyController;
use App\Http\Cms\TransactionController;
use App\Http\Cms\UserController;
use Illuminate\Support\Facades\Route;

$slug = '[0-9]+';

// Public routes
Route::get('/ui', [\App\Http\Front\PageController::class, 'ui']);
Route::get('/media/resize/{width}/{height}/{path}', [\App\Http\Front\ImageController::class, 'resize'])
    ->where('path', '.*')
    ->whereNumber(['width', 'height'])
    ->name('image.resize');

// BLOG
Route::group(['prefix' => '/blog', 'as' => 'blog.'], function () {
    Route::get('/', [\App\Http\Front\BlogController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [\App\Http\Front\BlogController::class, 'index'])->name('category');
    Route::get('/{post:slug}', [\App\Http\Front\BlogController::class, 'show'])->name('show');
});

Route::get('/tutoriels', [\App\Http\Front\CourseController::class, 'index'])->name('courses.index');
Route::get('/tutoriels/{slug}-{course}', [\App\Http\Front\CourseController::class, 'show'])
    ->whereNumber('course')
    ->middleware(\App\Http\Middleware\RedirectIfSlugMismatch::class.':course')
    ->name('courses.show');

// Admin routes
Route::group(['prefix' => '/cms', 'as' => 'cms.'], function () {
    Route::get('/', function () {
        return redirect('/cms/dashboard');
    });
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::resource('blog_categories', BlogCategoryController::class);
    Route::resource('comments', CommentController::class)->only(['index', 'update', 'destroy']);
    Route::resource('courses', CourseController::class);
    Route::resource('formations', FormationController::class)->except(['show']);
    Route::resource('paths', PathController::class)->except(['show']);
    Route::resource('posts', PostController::class)->except(['show']);
    Route::resource('plans', PlanController::class)->except(['edit', 'create']);
    Route::resource('technologies', TechnologyController::class)->except(['show']);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    Route::resource('transactions', TransactionController::class)->only(['index', 'destroy']);
    Route::resource('settings', SettingsController::class)->only(['index', 'store']);
    Route::get('search', [\App\Http\Cms\SearchController::class, 'search'])->name('search');

    // Attachments (JSON API)
    Route::get('attachments/folders', [AttachmentController::class, 'folders'])->name('attachments.folders');
    Route::resource('attachments', AttachmentController::class)->only(['store', 'destroy', 'index']);
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
