<?php

namespace App\Providers;

use App\Domains\Blog\Post;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Http\Front\AuthController;
use App\Infrastructure\Twitch\TwitchAPI;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('slug', '[a-z0-9\-]+');
        Route::pattern('driver', implode('|', AuthController::DRIVERS));
        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // When cache is disabled, autoload relationships to make dev env faster
        if (config('cache.default') === 'array') {
            Model::automaticallyEagerLoadRelationships();
        }

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

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
