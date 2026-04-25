<?php

namespace App\Infrastructure\Blade;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider to add blade directives
 */
class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php
\$__cache_directive_key = App\Infrastructure\Blade\BladeServiceProvider::cacheKey([{$expression}]);
\$__cache_directive_ttl = 3600;

if (\Illuminate\Support\Facades\Cache::has(\$__cache_directive_key)) {
    echo \Illuminate\Support\Facades\Cache::get(\$__cache_directive_key);
} else {
    \$__cache_directive_buffering = true;

    ob_start();
?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
\$__cache_directive_buffer = ob_get_clean();

\Illuminate\Support\Facades\Cache::put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);

echo \$__cache_directive_buffer;

unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments);
}
?>";
        });
    }

    /**
     * Compute a cache key for views
     */
    public static function cacheKey(mixed $expression): string
    {
        if (is_array($expression)) {
            return implode('-', array_map(cache_key(...), $expression));
        }
        if ($expression instanceof Model) {
            /** @var object{id: int, updated_at: DateTimeInterface} $expression */
            return sprintf('%s-%s', $expression->id, $expression->updated_at->getTimestamp());
        }
        if (is_bool($expression)) {
            return $expression ? '1' : '0';
        }
        if (is_null($expression)) {
            return '0';
        }

        return $expression;
    }
}
