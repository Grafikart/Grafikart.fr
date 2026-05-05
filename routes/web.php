<?php

use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [\App\Http\Front\HomeController::class, 'index'])->name('home');

// Auth
Route::get('/oauth/connect/{driver}', [\App\Http\Front\AuthController::class, 'connect'])->name('oauth');
Route::get('/oauth/check/{driver}', [\App\Http\Front\AuthController::class, 'callback'])->name('oauth.callback');
Route::get('/auth/check/premium', [\App\Http\Front\AuthController::class, 'checkPremium'])->name('auth.check.premium');

// Auth restricted page
Route::middleware(['auth', 'verified'])->group(function () {

    // User profil
    Route::get('/profil', [\App\Http\Front\UserController::class, 'edit'])->name('users.edit');
    Route::post('/profil', [\App\Http\Front\UserController::class, 'update']);
    Route::delete('/profil', [\App\Http\Front\UserController::class, 'delete'])->name('users.delete');
    Route::get('/profil/historique', [\App\Http\Front\UserController::class, 'history'])->name('users.history');
    Route::get('/profil/badges', [\App\Http\Front\UserController::class, 'badges'])->name('users.badges');
    Route::get('/profil/factures', [\App\Http\Front\Account\InvoiceController::class, 'index'])->name('transactions.index');
    Route::post('/profil/factures', [\App\Http\Front\Account\InvoiceController::class, 'update'])->name('transactions.update');
    Route::get('/profil/factures/{transaction}', [\App\Http\Front\Account\InvoiceController::class, 'show'])
        ->name('transactions.show');
    Route::post('/profil/subscription', [\App\Http\Front\Account\SubscriptionController::class, 'manage'])->name('users.subscription');
    Route::post('/profil/coupon', [\App\Http\Front\CouponController::class, 'claim'])->middleware('throttle:3')->name('users.coupon');
    Route::post('/profil/password', [\App\Http\Front\UserController::class, 'password'])->name('users.password');

    // School
    Route::get('/ecole', [\App\Http\Front\SchoolController::class, 'show'])->name('schools.show');
    Route::get('/student/{student}', [\App\Http\Front\SchoolController::class, 'student'])->name('schools.student');
    Route::post('/ecole', [\App\Http\Front\SchoolController::class, 'import'])->name('schools.import');

    // Misc
    Route::get('/notifications', [\App\Http\Front\NotificationController::class, 'index'])->name('notifications');
    Route::get('/oauth/unlink/{driver}', [\App\Http\Front\AuthController::class, 'unlink'])->name('oauth.unlink');
    Route::get('/tutoriels/{course}/download/{type}', [\App\Http\Front\CourseController::class, 'download'])->name('courses.download')->where('type', 'source|video');
    Route::get('/profil/revisions', [\App\Http\Front\RevisionController::class, 'index'])->name('revisions.index');

    // Revision content
    Route::get('/revision/{type}/{id}', [\App\Http\Front\RevisionController::class, 'edit'])->whereIn('type', ['course', 'post'])->whereNumber('id')->name('revision.edit');
    Route::post('/revision/{type}/{id}', [\App\Http\Front\RevisionController::class, 'update'])->whereIn('type', ['course', 'post'])->whereNumber('id')->name('revision.update');
    Route::delete('/revision/{revision}', [\App\Http\Front\RevisionController::class, 'delete'])->name('revision.delete');
});

// Images / Assets
Route::get('/media/resize/{width}/{height}/{path}', [\App\Http\Front\ImageController::class, 'resize'])
    ->where('path', '.*')
    ->whereNumber(['width', 'height'])
    ->name('image.resize');

// Pages
if (! app()->isProduction()) {
    Route::get('/ui', [\App\Http\Front\PageController::class, 'ui'])->name('pages.ui');
}
Route::get('/sponsors', [\App\Http\Front\SponsorController::class, 'index'])->name('pages.sponsors');
Route::get('/a-propos', [\App\Http\Front\PageController::class, 'about'])->name('pages.about');
Route::get('/politique-de-confidentialite', [\App\Http\Front\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [\App\Http\Front\PageController::class, 'terms'])->name('pages.terms');
Route::get('/premium', [\App\Http\Front\PageController::class, 'premium'])->name('premium');
Route::get('/premium/ecoles', [\App\Http\Front\PageController::class, 'school'])->name('pages.school');
Route::get('/contact', [\App\Http\Front\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Front\ContactController::class, 'submit']);
Route::get('/tchat', fn () => redirect('https://discordapp.com/invite/rAuuD7Q'))->name('tchat');

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
Route::get('/formations/{formation:slug}/continue', [\App\Http\Front\FormationController::class, 'continue'])
    ->name('formations.continue');
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

// RSS Feed
Route::get('/feed.rss', [\App\Http\Front\FeedController::class, 'index'])
    ->name('feed.rss')
    ->middleware(\Spatie\ResponseCache\Middlewares\CacheResponse::for(\Illuminate\Support\minutes(15)));

// Admin routes
Route::group([
    'prefix' => '/cms',
    'as' => 'cms.',
    'middleware' => ['auth', 'can:manageSite', \App\Http\Middleware\HandleInertiaRequests::class],
], function () {
    Route::get('/', function () {
        return redirect('/cms/dashboard');
    });
    Route::resource('badges', \App\Http\Cms\BadgeController::class)->except(['show']);
    Route::get('dashboard', [\App\Http\Cms\DashboardController::class, 'index'])->name('dashboard');
    Route::post('dashboard/cache', [\App\Http\Cms\DashboardController::class, 'clearCache'])->name('dashboard.cache.clear');
    Route::post('dashboard/notifications', [\App\Http\Cms\DashboardController::class, 'notification'])->name('notifications.store');
    Route::post('dashboard/email', [\App\Http\Cms\DashboardController::class, 'emailTest'])->name('dashboard.email.test');
    Route::resource('blog_categories', \App\Http\Cms\BlogCategoryController::class);
    Route::resource('comments', \App\Http\Cms\CommentController::class)->only(['index', 'update', 'destroy']);
    Route::resource('coupons', \App\Http\Cms\CouponController::class)->except(['show']);
    Route::get('courses/upload', [\App\Http\Cms\CourseController::class, 'upload'])->name('courses.upload');
    Route::resource('courses', \App\Http\Cms\CourseController::class);
    Route::apiResource('courses.questions', \App\Http\Cms\QuestionController::class)->except(['show'])->shallow();
    Route::post('courses/{course}/questions/import', [\App\Http\Cms\QuestionController::class, 'import'])->name('courses.questions.import');
    Route::resource('formations', \App\Http\Cms\FormationController::class)->except(['show']);
    Route::resource('paths', \App\Http\Cms\PathController::class)->except(['show']);
    Route::resource('posts', \App\Http\Cms\PostController::class)->except(['show']);
    Route::resource('plans', \App\Http\Cms\PlanController::class)->except(['edit', 'create']);
    Route::resource('schools', \App\Http\Cms\SchoolController::class)->except(['show']);
    Route::resource('support', \App\Http\Cms\SupportController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('contact_requests', \App\Http\Cms\ContactRequestController::class)->only(['index', 'show', 'destroy']);
    Route::resource('sponsors', \App\Http\Cms\SponsorController::class)->except(['show']);
    Route::resource('technologies', \App\Http\Cms\TechnologyController::class)->except(['show']);
    Route::get('users/search', [\App\Http\Cms\UserController::class, 'search'])->name('users.search');
    Route::resource('users', \App\Http\Cms\UserController::class)->only(['index', 'destroy']);
    Route::resource('transactions', \App\Http\Cms\TransactionController::class)->only(['index', 'destroy']);
    Route::get('transactions/report', [\App\Http\Cms\TransactionController::class, 'report'])->name('transactions.report');
    Route::resource('settings', \App\Http\Cms\SettingsController::class)->only(['index', 'store']);
    Route::delete('jobs/{job}', [\App\Http\Cms\JobController::class, 'destroy'])->name('jobs.destroy');
    Route::delete('failed-jobs/{job}', [\App\Http\Cms\JobController::class, 'destroyFailed'])->name('failed-jobs.destroy');
    Route::post('failed-jobs/{job}/retry', [\App\Http\Cms\JobController::class, 'retryFailed'])->name('failed-jobs.retry');
    Route::delete('failed-jobs', [\App\Http\Cms\JobController::class, 'flushFailed'])->name('failed-jobs.flush');
    Route::get('revisions', [\App\Http\Cms\RevisionController::class, 'index'])->name('revisions.index');
    Route::get('revisions/{revision}', [\App\Http\Cms\RevisionController::class, 'show'])->name('revisions.show');
    Route::post('revisions/{revision}', [\App\Http\Cms\RevisionController::class, 'update'])->name('revisions.update');
    Route::get('search', [\App\Http\Cms\SearchController::class, 'search'])->name('search');
    Route::post('twitch', [\App\Http\Cms\TwitchController::class, 'store'])->name('twitch.store');

    // Attachments (JSON API)
    Route::get('attachments/folders', [\App\Http\Cms\AttachmentController::class, 'folders'])->name('attachments.folders');
    Route::resource('attachments', \App\Http\Cms\AttachmentController::class)->only(['store', 'destroy', 'index']);
});
