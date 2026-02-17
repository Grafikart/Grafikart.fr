<?php

use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [\App\Http\Front\HomeController::class, 'index'])->name('home');

// Auth
Route::get('/oauth/connect/{driver}', [\App\Http\Front\AuthController::class, 'connect'])->name('oauth');
Route::get('/oauth/check/{driver}', [\App\Http\Front\AuthController::class, 'callback'])->name('oauth.callback');
Route::get('/auth/check/premium', [\App\Http\Front\AuthController::class, 'checkPremium'])->name('auth.check.premium');

// Auth restricted page
Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [\App\Http\Front\UserController::class, 'me'])->name('users.me');
    Route::delete('/profil', [\App\Http\Front\UserController::class, 'delete'])->name('users.delete');
    Route::get('/profil/edit', [\App\Http\Front\UserController::class, 'edit'])->name('users.edit');
    Route::get('/profil/factures', [\App\Http\Front\Account\InvoiceController::class, 'index'])->name('transactions.index');
    Route::post('/profil/factures', [\App\Http\Front\Account\InvoiceController::class, 'update'])->name('transactions.update');
    Route::get('/profil/factures/{transaction}', [\App\Http\Front\Account\InvoiceController::class, 'show'])
        ->name('transactions.show');
    Route::post('/profil/subscription', [\App\Http\Front\Account\SubscriptionController::class, 'manage'])->name('users.subscription');
    Route::post('/profil/edit', [\App\Http\Front\UserController::class, 'update']);
    Route::post('/profil/password', [\App\Http\Front\UserController::class, 'password'])->name('users.password');
    Route::get('/notifications', [\App\Http\Front\NotificationController::class, 'index'])->name('notifications');
    Route::get('/oauth/unlink/{driver}', [\App\Http\Front\AuthController::class, 'unlink'])->name('oauth.unlink');
});

// Pages
Route::get('/ui', [\App\Http\Front\PageController::class, 'ui'])->name('pages.ui');
Route::get('/a-propos', [\App\Http\Front\PageController::class, 'about'])->name('pages.about');
Route::get('/tchat', fn () => redirect('https://discordapp.com/invite/rAuuD7Q'))->name('tchat');

// Images / Assets
Route::get('/media/resize/{width}/{height}/{path}', [\App\Http\Front\ImageController::class, 'resize'])
    ->where('path', '.*')
    ->whereNumber(['width', 'height'])
    ->name('image.resize');

// Page
Route::get('/politique-de-confidentialite', [\App\Http\Front\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/premium', [\App\Http\Front\PageController::class, 'premium'])->name('premium');
Route::get('/contact', [\App\Http\Front\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Front\ContactController::class, 'submit']);

// Forum
Route::group(['prefix' => '/forum', 'as' => 'forum.'], function () {
    Route::get('/', [\App\Http\Front\ForumController::class, 'index'])->name('index');
    Route::get('/{slug}-{tag}', [\App\Http\Front\ForumController::class, 'tag'])
        ->whereNumber('tag')
        ->middleware(\App\Http\Middleware\RedirectIfSlugMismatch::class.':tag')
        ->name('tag');
    Route::get('/{topic}', [\App\Http\Front\ForumController::class, 'topic'])->name('topic')->whereNumber('topic');
});

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
Route::get('/tutoriels/{technology:slug}', [\App\Http\Front\TechnologyController::class, 'show'])->name('technologies.show');
Route::get('/formations', [\App\Http\Front\FormationController::class, 'index'])->name('formations.index');
Route::get('/formations/{formation:slug}', [\App\Http\Front\FormationController::class, 'show'])
    ->name('formations.show');
Route::get('/recherche', [\App\Http\Front\SearchController::class, 'index'])->name('search.index');
Route::get('/live', [\App\Http\Front\LiveController::class, 'show'])->name('live');

// Blog
Route::group(['prefix' => '/blog', 'as' => 'blog.'], function () {
    Route::get('/', [\App\Http\Front\BlogController::class, 'index'])->name('index');
    Route::get('/category/{category:slug}', [\App\Http\Front\BlogController::class, 'index'])->name('category');
    Route::get('/{post:slug}', [\App\Http\Front\BlogController::class, 'show'])->name('show');
});

// Admin routes
Route::group([
    'prefix' => '/cms',
    'as' => 'cms.',
    'middleware' => ['auth', 'can:manageSite', \App\Http\Middleware\HandleInertiaRequests::class],
], function () {
    Route::get('/', function () {
        return redirect('/cms/dashboard');
    });
    Route::get('dashboard', [\App\Http\Cms\DashboardController::class, 'index'])->name('dashboard');
    Route::post('dashboard/notifications', [\App\Http\Cms\DashboardController::class, 'notification'])->name('notifications.store');
    Route::resource('blog_categories', \App\Http\Cms\BlogCategoryController::class);
    Route::resource('comments', \App\Http\Cms\CommentController::class)->only(['index', 'update', 'destroy']);
    Route::get('courses/upload', [\App\Http\Cms\CourseController::class, 'upload'])->name('courses.upload');
    Route::resource('courses', \App\Http\Cms\CourseController::class);
    Route::apiResource('courses.questions', \App\Http\Cms\QuestionController::class)->except(['show'])->shallow();
    Route::post('courses/{course}/questions/import', [\App\Http\Cms\QuestionController::class, 'import'])->name('courses.questions.import');
    Route::resource('formations', \App\Http\Cms\FormationController::class)->except(['show']);
    Route::resource('paths', \App\Http\Cms\PathController::class)->except(['show']);
    Route::resource('posts', \App\Http\Cms\PostController::class)->except(['show']);
    Route::resource('plans', \App\Http\Cms\PlanController::class)->except(['edit', 'create']);
    Route::resource('technologies', \App\Http\Cms\TechnologyController::class)->except(['show']);
    Route::resource('users', \App\Http\Cms\UserController::class)->only(['index', 'destroy']);
    Route::resource('transactions', \App\Http\Cms\TransactionController::class)->only(['index', 'destroy']);
    Route::resource('settings', \App\Http\Cms\SettingsController::class)->only(['index', 'store']);
    Route::delete('jobs/{job}', [\App\Http\Cms\JobController::class, 'destroy'])->name('jobs.destroy');
    Route::delete('failed-jobs/{job}', [\App\Http\Cms\JobController::class, 'destroyFailed'])->name('failed-jobs.destroy');
    Route::post('failed-jobs/{job}/retry', [\App\Http\Cms\JobController::class, 'retryFailed'])->name('failed-jobs.retry');
    Route::delete('failed-jobs', [\App\Http\Cms\JobController::class, 'flushFailed'])->name('failed-jobs.flush');
    Route::get('search', [\App\Http\Cms\SearchController::class, 'search'])->name('search');
    Route::post('twitch', [\App\Http\Cms\TwitchController::class, 'store'])->name('twitch.store');

    // Attachments (JSON API)
    Route::get('attachments/folders', [\App\Http\Cms\AttachmentController::class, 'folders'])->name('attachments.folders');
    Route::resource('attachments', \App\Http\Cms\AttachmentController::class)->only(['store', 'destroy', 'index']);
});
