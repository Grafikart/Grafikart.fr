<?php

namespace App\Providers;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Http\Front\AuthController;
use App\Infrastructure\Twitch\TwitchAPI;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TwitchAPI::class, fn () => new TwitchAPI(
            id: config('services.twitch.id'),
            secret: config('services.twitch.secret'),
        ));
        $this->app->singleton(\Google_Client::class, function () {
            $client = new \Google_Client([]);
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));

            return $client;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPermissions();
        $this->registerRoutePatterns();
        $this->registerViewGlobals();
        $this->configureDefaults();
    }

    private function registerPermissions(): void
    {
        Gate::before(function (User $user) {
            if ($user->isAdmin()) {
                return true;
            }

            return null;
        });
    }

    private function registerRoutePatterns()
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('slug', '[a-z0-9\-]+');
        Route::pattern('driver', implode('|', AuthController::DRIVERS));
    }

    private function registerViewGlobals()
    {
        // Add appearance (theme) to the view
        View::composer(['front', 'cms'], function (\Illuminate\View\View $view): void {
            $view->with('appearance', request()->cookie('appearance'));
        });

        // Inject the authenticated user for all the views
        View::composer('*', function (\Illuminate\View\View $view): void {
            $view->with('user', auth()->user());
        });

    }

    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // When cache is disabled, autoload relationships to make dev env faster
        Model::automaticallyEagerLoadRelationships();

        Relation::enforceMorphMap([
            'post' => Post::class,
            'formation' => Formation::class,
            'course' => Course::class,
            // Fake morph type for the paths
            'gate' => Course::class,
            'fork' => Course::class,
        ]);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        //        Password::defaults(fn (): ?Password => app()->isProduction()
        //            ? Password::min(12)
        //                ->mixedCase()
        //                ->letters()
        //                ->numbers()
        //                ->symbols()
        //                ->uncompromised()
        //            : null
        //        );
    }
}
